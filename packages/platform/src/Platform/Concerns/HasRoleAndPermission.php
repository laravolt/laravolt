<?php

namespace Laravolt\Platform\Concerns;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravolt\Platform\Models\Permission;

trait HasRoleAndPermission
{
    public function roles()
    {
        return $this->belongsToMany(config('laravolt.epicentrum.models.role'), 'acl_role_user', 'user_id', 'role_id');
    }

    public function permissions(): Collection
    {
        return Cache::driver('array')->rememberForever("users.{$this->getKey()}.permissions", function() {
            /** @var Permission $permissionModel */
            $permissionModel = app(config('laravolt.epicentrum.models.permission'));

            return $permissionModel
                ->newModelQuery()
                ->selectRaw('acl_permissions.*')
                ->join('acl_permission_role', 'acl_permissions.id', '=', 'acl_permission_role.permission_id')
                ->join('acl_role_user', 'acl_role_user.role_id', '=', 'acl_permission_role.role_id')
                ->join('users', 'users.id', '=', 'acl_role_user.user_id')
                ->where('users.id', $this->getKey())
                ->get()->unique();
        });
    }

    public function getPermissionsAttribute()
    {
        return $this->permissions();
    }

    public function assignRole($role): self
    {
        if (is_array($role)) {
            foreach ($role as $r) {
                $this->assignRole($r);
            }

            return $this;
        }

        if (is_string($role) && ! Str::isUuid($role)) {
            $role = app(config('laravolt.epicentrum.models.role'))->firstOrCreate(['name' => $role]);
        }

        $this->roles()->syncWithoutDetaching($role);

        return $this;
    }

    public function revokeRole($role): self
    {
        if (is_array($role)) {
            foreach ($role as $r) {
                $this->revokeRole($r);
            }

            return $this;
        }

        if (is_string($role) && ! Str::isUuid($role)) {
            $role = app(config('laravolt.epicentrum.models.role'))->where('name', $role)->first();
        }

        $this->roles()->detach($role);

        return $this;
    }

    public function hasRole($role, $checkAll = false): bool
    {
        if (is_array($role)) {
            $match = 0;
            foreach ($role as $r) {
                $match += (int) $this->hasRole($r, $checkAll);
            }

            if ($checkAll) {
                return $match == count($role);
            } else {
                return $match > 0;
            }
        }

        if (Str::isUuid($role)) {
            $role = $this->roles->firstWhere('id', $role);
        }

        if (is_string($role)) {
            $role = $this->roles->firstWhere('name', $role);
        }

        if (is_int($role)) {
            $role = $this->roles->firstWhere('id', $role);
        }

        if (! $role instanceof Model) {
            return false;
        }

        foreach ($this->roles as $assignedRole) {
            if ($role->is($assignedRole)) {
                return true;
            }
        }

        return false;
    }

    public function syncRoles($roles): self
    {
        $ids = collect($roles)->transform(function ($role) {
            if (is_numeric($role)) {
                return (int) $role;
            } elseif (is_string($role)) {
                $role = app(config('laravolt.epicentrum.models.role'))->firstOrCreate(['name' => $role]);

                return $role->getKey();
            } elseif ($role instanceof Model) {
                return $role->getKey();
            }
        })->filter(function ($id) {
            return $id > 0;
        });

        $this->roles()->sync($ids);

        return $this;
    }

    public function hasPermission($permission, $checkAll = false): bool
    {
        return once(function () use ($permission, $checkAll) {
            return $this->_hasPermission($permission, $checkAll);
        });
    }

    protected function _hasPermission($permission, $checkAll = false): bool
    {
        if (is_array($permission)) {
            $match = 0;
            foreach ($permission as $perm) {
                $match += (int) $this->hasPermission($perm);
            }

            if ($checkAll) {
                return $match == count($permission);
            } else {
                return $match > 0;
            }
        }

        if (Str::isUuid($permission)) {
            return (bool) $this->permissions()->firstWhere('id', $permission);
        }

        if (is_string($permission)) {
            return (bool) $this->permissions()->firstWhere('name', $permission);
        }

        if (is_int($permission)) {
            return (bool) $this->permissions()->firstWhere('id', $permission);
        }

        return false;
    }
}
