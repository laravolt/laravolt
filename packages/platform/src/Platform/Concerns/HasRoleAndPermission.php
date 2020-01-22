<?php

namespace Laravolt\Platform\Concerns;

use Illuminate\Database\Eloquent\Model;

trait HasRoleAndPermission
{
    public function roles()
    {
        return $this->belongsToMany(config('laravolt.acl.models.role'), 'acl_role_user', 'user_id', 'role_id');
    }

    public function assignRole($role): self
    {
        if (is_array($role)) {
            foreach ($role as $r) {
                $this->assignRole($r);
            }

            return $this;
        }

        if (is_string($role)) {
            $role = app(config('laravolt.acl.models.role'))->firstOrCreate(['name' => $role]);
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

        if (is_string($role)) {
            $role = app(config('laravolt.acl.models.role'))->where('name', $role)->first();
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

    public function syncRoles($roles): self
    {
        $ids = collect($roles)->transform(function ($role) {
            if (is_numeric($role)) {
                return (int) $role;
            } elseif (is_string($role)) {
                $role = app(config('laravolt.acl.models.role'))->firstOrCreate(['name' => $role]);

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

        if (is_string($permission)) {
            $permission = app(config('laravolt.acl.models.permission'))->where('name', $permission)->first();
        }

        if (is_int($permission)) {
            $permission = app(config('laravolt.acl.models.permission'))->find($permission);
        }

        if (!$permission instanceof Model) {
            throw new \InvalidArgumentException('Argument must be integer, existing permission name, or an instance of '.config('laravolt.acl.models.permission'));
        }

        foreach ($this->roles as $assignedRole) {
            if ($assignedRole->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }
}
