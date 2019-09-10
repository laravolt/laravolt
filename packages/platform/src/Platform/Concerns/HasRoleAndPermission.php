<?php

namespace Laravolt\Platform\Concerns;

use Illuminate\Database\Eloquent\Model;

trait HasRoleAndPermission
{
    public function roles()
    {
        return $this->belongsToMany(config('laravolt.acl.models.role'), 'acl_role_user', 'user_id', 'role_id');
    }

    public function hasRole($role, $checkAll = false)
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

        if (is_string($role)) {
            $role = $this->roles->firstWhere('name', $role);
        }

        if (is_int($role)) {
            $role = $this->roles->firstWhere('id', $role);
        }

        if (!$role instanceof Model) {
            return false;
        }

        foreach ($this->roles as $assignedRole) {
            if ($role->is($assignedRole)) {
                return true;
            }
        }

        return false;
    }

    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = app(config('laravolt.acl.models.role'))->where('name', $role)->first();
        }

        return $this->roles()->syncWithoutDetaching($role);
    }

    public function revokeRole($role)
    {
        if (is_string($role)) {
            $role = app(config('laravolt.acl.models.role'))->where('name', $role)->first();
        }

        return $this->roles()->detach($role);
    }

    public function syncRoles($roles)
    {
        $ids = collect($roles)->transform(function ($role) {
            if (is_numeric($role)) {
                return (int) $role;
            } elseif (is_string($role)) {
                $role = app(config('laravolt.acl.models.role'))->firstOrCreate(['name' => $role]);

                return $role->getKey();
            }
        })->filter(function ($id) {
            return $id > 0;
        });

        return $this->roles()->sync($ids);
    }

    public function hasPermission($permission, $checkAll = false)
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

        if (is_string($permission)) {
            $permission = app(config('laravolt.acl.models.permission'))->where('name', $permission)->first();
        }

        if (is_int($permission)) {
            $permission = app(config('laravolt.acl.models.permission'))->find($permission);
        }

        if (!$permission instanceof Model) {
            throw new \InvalidArgumentException('Argument must be integer, string, or an instance of '.Model::class);
        }

        foreach ($this->roles as $assignedRole) {
            foreach ($assignedRole->permissions as $assignedPermission) {
                if ($permission->is($assignedPermission)) {
                    return true;
                }
            }
        }

        return false;
    }
}
