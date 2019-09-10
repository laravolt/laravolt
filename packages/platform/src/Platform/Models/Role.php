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

    public function addPermission($permission)
    {
        if (is_string($permission)) {
            $permission = app(config('laravolt.acl.models.permission'))->firstOrCreate(['name' => $permission]);
        }

        return $this->permissions()->attach($permission);
    }

    public function removePermission($permission)
    {
        if (is_string($permission)) {
            $permission = app(config('laravolt.acl.models.permission'))->firstOrCreate(['name' => $permission]);
        }

        return $this->permissions()->detach($permission);
    }

    public function syncPermission(array $permissions)
    {
        $ids = collect($permissions)->transform(function ($permission) {
            if (is_numeric($permission)) {
                return (int)$permission;
            } elseif (is_string($permission)) {
                $permissionObject = app(config('laravolt.acl.models.permission'))->firstOrCreate(['name' => $permission]);

                return $permissionObject->id;
            }
        })->filter(function ($id) {
            return $id > 0;
        });

        return $this->permissions()->sync($ids->toArray());
    }

}
