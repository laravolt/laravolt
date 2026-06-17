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
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(config('laravolt.epicentrum.models.role'), 'acl_role_user', 'user_id', 'role_id');
    }

    public function permissions(): Collection
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
                ->map(
                    fn ($permission) => [
                    'id' => $permission->getKey(),
                    'name' => (string) ($permission->name ?? ''),
                    ]
                )
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

        $permissionModel = app(config('laravolt.epicentrum.models.permission'));
        $models = array_map(
            function (array $row) use ($permissionModel) {
                $instance = $permissionModel->newInstance([], true);
                $instance->setRawAttributes(['id' => $row['id'], 'name' => $row['name']], true);

                return $instance;
            },
            $cached
        );

        return new Collection($models);
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

        if (Str::isUuid($role)) {
            return $this->roles->contains('id', $role);
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
        $result = once(
            function () use ($permission, $checkAll) {
                return $this->_hasPermission($permission, $checkAll);
            }
        );

        return $result;
    }

    protected function resolveRoleIds($roles, bool $createMissing = false): array
    {
        $rolesArray = is_array($roles) ? $roles : [$roles];

        // ⚡ Bolt: Prevent N+1 queries when resolving string roles
        $roleModel = app(config('laravolt.epicentrum.models.role'));
        $stringNames = [];
        foreach ($rolesArray as $role) {
            if (is_string($role) && ! Str::isUuid($role) && ! Str::isUlid($role)) {
                $stringNames[] = $role;
            }
        }

        $existingModels = [];
        if (! empty($stringNames)) {
            $existingModels = $roleModel->whereIn('name', $stringNames)->get()->keyBy('name');
        }

        return collect($rolesArray)
            ->map(
                function ($role) use ($createMissing, $existingModels, $roleModel) {
                    if (is_numeric($role)) {
                        return (int) $role;
                    }

                    if (is_string($role) && (Str::isUuid($role) || Str::isUlid($role))) {
                        return $role;
                    }

                    if (is_string($role)) {
                        if (isset($existingModels[$role])) {
                            return $existingModels[$role]->getKey();
                        }
                        if ($createMissing) {
                            $newRole = $roleModel->firstOrCreate(['name' => $role]);

                            return $newRole->getKey();
                        }

                        return null;
                    }

                    if ($role instanceof Model) {
                        return $role->getKey();
                    }

                    return $role;
                }
            )
            ->filter(
                function ($id) {
                    if (is_int($id)) {
                        return $id > 0;
                    }

                    if (is_string($id)) {
                        return trim($id) !== '';
                    }

                    return false;
                }
            )
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

        if (Str::isUuid($permission)) {
            return $this->permissions()->contains('id', $permission);
        }

        if (is_string($permission)) {
            return $this->permissions()->contains('name', $permission);
        }

        if (is_int($permission)) {
            return $this->permissions()->contains('id', $permission);
        }

        if ($permission instanceof Model) {
            return $this->permissions()->contains($permission->getKeyName(), $permission->getKey());
        }

        return false;
    }
}
