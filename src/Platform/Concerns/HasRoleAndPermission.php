<?php

declare(strict_types=1);

namespace Laravolt\Platform\Concerns;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravolt\Platform\Models\Permission;
use Laravolt\Platform\Services\AccessControlInvalidator;

trait HasRoleAndPermission
{
    /**
     * @var array{key: mixed, generation: int, lookup: array{rows: list<array{id: int|string, name: string}>, ids: array<int|string, true>, names: array<string, true>}}|null
     */
    protected ?array $laravoltPermissionLookup = null;

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(config('laravolt.epicentrum.models.role'), 'acl_role_user', 'user_id', 'role_id');
    }

    public function permissions(): Collection
    {
        $permissionModel = app(config('laravolt.epicentrum.models.permission'));
        $models = array_map(
            function (array $row) use ($permissionModel) {
                $instance = $permissionModel->newInstance([], true);
                $instance->setRawAttributes(['id' => $row['id'], 'name' => $row['name']], true);

                return $instance;
            },
            $this->permissionLookup()['rows']
        );

        return new Collection($models);
    }

    /**
     * The user's permissions (id + name), indexed for hasPermission().
     *
     * Read from the cache once per model instance, so a page that checks
     * twenty permissions (sidebar items, gates, policies) reads the cache once
     * instead of twenty times. Reloaded when the user's key changes or when
     * AccessControlInvalidator invalidates any user in this process (role or
     * permission changes), so a change made earlier in the same request or
     * job is seen right away.
     *
     * @return array{rows: list<array{id: int|string, name: string}>, ids: array<int|string, true>, names: array<string, true>}
     */
    protected function permissionLookup(): array
    {
        $key = $this->getKey();
        $generation = AccessControlInvalidator::generation();
        $memo = $this->laravoltPermissionLookup;

        if ($memo !== null && $memo['key'] === $key && $memo['generation'] === $generation) {
            return $memo['lookup'];
        }

        $rows = $this->cachedPermissionRows();
        $lookup = ['rows' => $rows, 'ids' => [], 'names' => []];
        foreach ($rows as $row) {
            if (is_int($row['id']) || is_string($row['id'])) {
                $lookup['ids'][$row['id']] = true;
            }
            $lookup['names'][$row['name']] = true;
        }

        $this->laravoltPermissionLookup = ['key' => $key, 'generation' => $generation, 'lookup' => $lookup];

        return $lookup;
    }

    /**
     * @return list<array{id: int|string, name: string}>
     */
    protected function cachedPermissionRows(): array
    {
        $cacheKey = "users.{$this->getKey()}.permissions";
        $rebuild = function (): array {
            /**
             * @var Permission $permissionModel
             */
            $permissionModel = app(config('laravolt.epicentrum.models.permission'));

            return $permissionModel
                ->newModelQuery()
                ->selectRaw('acl_permissions.*')
                ->join('acl_permission_role', 'acl_permissions.id', '=', 'acl_permission_role.permission_id')
                ->join('acl_role_user', 'acl_role_user.role_id', '=', 'acl_permission_role.role_id')
                ->join('users', 'users.id', '=', 'acl_role_user.user_id')
                ->where('users.id', $this->getKey())
                ->get()
                ->unique('id')
                ->map(fn ($permission) => [
                    'id' => $permission->getKey(),
                    'name' => (string) ($permission->name ?? ''),
                ])
                ->values()
                ->all();
        };

        // Cache primitive arrays (id+name) for 1 hour. Primitive shape survives
        // class moves / serialization drift; if the cached value is malformed
        // (e.g. __PHP_Incomplete_Class from a stale Collection), forget+rebuild.
        $cached = Cache::get($cacheKey);

        if (! $this->isValidPermissionCache($cached)) {
            Cache::forget($cacheKey);
            $cached = $rebuild();
            Cache::put($cacheKey, $cached, 3600);
        }

        return array_values($cached);
    }

    /**
     * Validate the cached permission shape: array of arrays each with id+name.
     */
    protected function isValidPermissionCache(mixed $value): bool
    {
        if (! is_array($value)) {
            return false;
        }

        foreach ($value as $row) {
            if (! is_array($row)) {
                return false;
            }

            if (! array_key_exists('id', $row) || ! array_key_exists('name', $row)) {
                return false;
            }

            if (! is_string($row['name'])) {
                return false;
            }
        }

        return true;
    }

    public function getPermissionsAttribute(): Collection
    {
        return $this->permissions();
    }

    public function assignRole($role): self
    {
        $changes = $this->roles()->syncWithoutDetaching($this->resolveRoleIds($role, true));

        if ($this->hasSyncChanges($changes)) {
            $this->unsetRelation('roles');
            $this->invalidateAccessControl();
        }

        return $this;
    }

    public function revokeRole($role): self
    {
        $detached = $this->roles()->detach($this->resolveRoleIds($role));

        if ($detached > 0) {
            $this->unsetRelation('roles');
            $this->invalidateAccessControl();
        }

        return $this;
    }

    public function hasRole($role, $checkAll = false): bool
    {
        if (is_array($role)) {
            foreach ($role as $r) {
                $has = $this->hasRole($r, $checkAll);

                if ($checkAll && ! $has) {
                    return false;
                }

                if (! $checkAll && $has) {
                    return true;
                }
            }

            return $checkAll;
        }

        if (is_string($role) && (Str::isUlid($role) || Str::isUuid($role))) {
            return $this->roles->contains(
                fn ($assigned) => $assigned->getKey() === $role || $assigned->name === $role
            );
        }

        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        if (is_int($role)) {
            return $this->roles->contains('id', $role);
        }

        if ($role instanceof Model) {
            return $this->roles->contains($role->getKeyName(), $role->getKey());
        }

        return false;
    }

    public function syncRoles($roles): self
    {
        $changes = $this->roles()->sync($this->resolveRoleIds($roles, true));

        if ($this->hasSyncChanges($changes)) {
            $this->unsetRelation('roles');
            $this->invalidateAccessControl();
        }

        return $this;
    }

    public function hasPermission($permission, $checkAll = false): bool
    {
        return $this->_hasPermission($permission, $checkAll);
    }

    protected function resolveRoleIds($roles, bool $createMissing = false): array
    {
        return collect(is_array($roles) ? $roles : [$roles])
            ->map(function ($role) use ($createMissing) {
                if (is_numeric($role)) {
                    return (int) $role;
                }

                if (is_string($role) && (Str::isUlid($role) || Str::isUuid($role))) {
                    return $role;
                }

                if (is_string($role)) {
                    $query = app(config('laravolt.epicentrum.models.role'))->where('name', $role);
                    $role = $createMissing ? $query->firstOrCreate(['name' => $role]) : $query->first();

                    return $role?->getKey();
                }

                if ($role instanceof Model) {
                    return $role->getKey();
                }

                return $role;
            })
            ->filter(function ($id) {
                if (is_int($id)) {
                    return $id > 0;
                }

                if (is_string($id)) {
                    return trim($id) !== '';
                }

                return false;
            })
            ->all();
    }

    protected function hasSyncChanges(array $changes): bool
    {
        return collect($changes)->flatten()->isNotEmpty();
    }

    protected function invalidateAccessControl(): void
    {
        app(AccessControlInvalidator::class)->invalidateUser($this);
    }

    protected function _hasPermission($permission, $checkAll = false): bool
    {
        if (is_array($permission)) {
            foreach ($permission as $perm) {
                $has = $this->hasPermission($perm);

                if ($checkAll && ! $has) {
                    return false;
                }

                if (! $checkAll && $has) {
                    return true;
                }
            }

            return $checkAll;
        }

        if ($permission instanceof Model && $permission->getKeyName() !== 'id') {
            return $this->permissions()->contains($permission->getKeyName(), $permission->getKey());
        }

        $lookup = $this->permissionLookup();

        if (is_string($permission)) {
            return isset($lookup['names'][$permission])
                || ((Str::isUlid($permission) || Str::isUuid($permission)) && isset($lookup['ids'][$permission]));
        }

        if (is_int($permission)) {
            return isset($lookup['ids'][$permission]);
        }

        if ($permission instanceof Model) {
            $key = $permission->getKey();

            return (is_int($key) || is_string($key)) && isset($lookup['ids'][$key]);
        }

        return false;
    }
}
