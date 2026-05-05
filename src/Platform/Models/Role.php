<?php

declare(strict_types=1);

namespace Laravolt\Platform\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasUlids;

    protected $table = 'acl_roles';

    protected $fillable = ['name'];

    protected $with = ['permissions'];

    public function permissions()
    {
        return $this->belongsToMany(config('laravolt.epicentrum.models.permission'), 'acl_permission_role');
    }

    public function users()
    {
        return $this->belongsToMany(config('laravolt.epicentrum.models.user'), 'acl_role_user');
    }

    public function addPermission($permission): self
    {
        if (is_string($permission)) {
            $permission = app(config('laravolt.epicentrum.models.permission'))->firstOrCreate(['name' => $permission]);
        }

        $this->permissions()->attach($permission);

        return $this;
    }

    public function removePermission($permission): self
    {
        if (is_string($permission)) {
            $permission = app(config('laravolt.epicentrum.models.permission'))->firstOrCreate(['name' => $permission]);
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
            if (str($permission)->isUlid()) {
                return (string) $permission;
            }
            if (is_numeric($permission)) {
                return (int) $permission;
            }
            if (is_string($permission)) {
                $permissionObject = app(config('laravolt.epicentrum.models.permission'))->firstOrCreate(['name' => $permission]);

                return $permissionObject->getKey();
            }
            if ($permission instanceof Model) {
                return $permission->getKey();
            }
        })->filter(function ($id) {
            return $id > 0;
        });

        return $this->permissions()->sync($ids->toArray());
    }

    protected function _hasPermission($permission)
    {
        $permissionModel = app(config('laravolt.epicentrum.models.permission'));
        $keyName = $permissionModel->getKeyName();

        if (is_int($permission)) {
            return $this->permissions->contains($keyName, $permission);
        }

        if (is_string($permission)) {
            return $this->permissions->contains($keyName, $permission) || $this->permissions->contains('name', $permission);
        }

        if ($permission instanceof Model) {
            return $this->permissions->contains($permission->getKeyName(), $permission->getKey());
        }

        return false;
    }
}
