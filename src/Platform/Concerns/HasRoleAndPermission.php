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
        // save users permissions result for 1 hour (3600 seconds)
        return Cache::remember(
            "users.{$this->getKey()}.permissions", 3600, function () {
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
                    ->get()->unique();
            }
        );
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
        $roles = is_array($roles) ? $roles : [$roles];

        $roleModel = app(config('laravolt.epicentrum.models.role'));

        // ⚡ Bolt: Bulk check for existing roles by string name to prevent N+1 queries
        $stringNames = [];
        foreach ($roles as $role) {
            if (is_string($role) && !Str::isUuid($role) && !Str::isUlid($role)) {
                $stringNames[] = $role;
            }
        }

        $existingRolesByName = collect();
        if (!empty($stringNames)) {
            $existingRolesByName = $roleModel->whereIn('name', $stringNames)->get()->keyBy('name');
        }

        return collect($roles)
            ->map(
                function ($role) use ($createMissing, $existingRolesByName, $roleModel) {
                    if (is_numeric($role)) {
                        return (int) $role;
                    }

                    if (is_string($role) && (Str::isUuid($role) || Str::isUlid($role))) {
                        return $role;
                    }

                    if (is_string($role)) {
                        if ($existingRolesByName->has($role)) {
                            return $existingRolesByName->get($role)->getKey();
                        }

                        if ($createMissing) {
                            return $roleModel->firstOrCreate(['name' => $role])->getKey();
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
