<?php

namespace Laravolt\Platform\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'acl_roles';

    protected $fillable = ['name'];

    public function permissions()
    {
        return $this->belongsToMany(config('laravolt.acl.models.permission'), 'acl_permission_role');
    }

    public function users()
    {
        return $this->belongsToMany(config('auth.providers.users.model'), 'acl_role_user');
    }

    public function addPermission($permission): self
    {
        if (is_string($permission)) {
            $permission = app(config('laravolt.acl.models.permission'))->firstOrCreate(['name' => $permission]);
        }

        $this->permissions()->attach($permission);

        return $this;
    }

    public function removePermission($permission): self
    {
        if (is_string($permission)) {
            $permission = app(config('laravolt.acl.models.permission'))->firstOrCreate(['name' => $permission]);
        }

        $this->permissions()->detach($permission);

        return $this;
    }

    public function hasPermission($permission)
    {
        return once(function () use ($permission) {
            return $this->_hasPermission($permission);
        });
    }

    public function syncPermission(array $permissions)
    {
        $ids = collect($permissions)->transform(function ($permission) {
            if (is_numeric($permission)) {
                return (int) $permission;
            } elseif (is_string($permission)) {
                $permissionObject = app(config('laravolt.acl.models.permission'))->firstOrCreate(['name' => $permission]);

                return $permissionObject->getKey();
            } elseif ($permission instanceof Model) {
                return $permission->getKey();
            }
        })->filter(function ($id) {
            return $id > 0;
        });

        return $this->permissions()->sync($ids->toArray());
    }

    protected function _hasPermission($permission)
    {
        $model = $permission;

        if (!$permission instanceof Model) {
            $model = app(config('laravolt.acl.models.permission'))->find($permission);
            if (!$model) {
                $model = app(config('laravolt.acl.models.permission'))->where('name', $permission)->first();
            }
        }

        if (!$model instanceof Model) {
            return false;
        }

        foreach ($this->permissions as $assignedPermission) {
            if ($model->is($assignedPermission)) {
                return true;
            }
        }

        return false;
    }
}
