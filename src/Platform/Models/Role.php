<?php

declare(strict_types=1);

namespace Laravolt\Platform\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravolt\Platform\Services\AccessControlInvalidator;

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
        $this->unsetRelation('permissions');
        $this->invalidateAssignedUsersAccessControl();

        return $this;
    }

    public function removePermission($permission): self
    {
        if (is_string($permission)) {
            $permission = app(config('laravolt.epicentrum.models.permission'))->firstOrCreate(['name' => $permission]);
        }

        $this->permissions()->detach($permission);
        $this->unsetRelation('permissions');
        $this->invalidateAssignedUsersAccessControl();

        return $this;
    }

    public function hasPermission($permission)
    {
        return once(
            function () use ($permission) {
                return $this->_hasPermission($permission);
            }
        );
    }

    public function syncPermission(array $permissions)
    {
        // ⚡ Bolt: Bulk fetch existing permissions by name to prevent N+1 queries when synching
        // numerous string-based permissions.
        $stringNames = [];
        foreach ($permissions as $permission) {
            if (is_string($permission) && ! str($permission)->isUlid() && ! is_numeric($permission)) {
                $stringNames[] = $permission;
            }
        }

        $existingPermissions = collect();
        if (! empty($stringNames)) {
            $existingPermissions = app(config('laravolt.epicentrum.models.permission'))
                ->whereIn('name', $stringNames)
                ->get()
                ->keyBy('name');
        }

        $ids = collect($permissions)->transform(
            function ($permission) use ($existingPermissions) {
                if (is_string($permission) && str($permission)->isUlid()) {
                    return $permission;
                }
                if (is_numeric($permission)) {
                    return (int) $permission;
                }
                if (is_string($permission)) {
                    if ($existingPermissions->has($permission)) {
                        return $existingPermissions->get($permission)->getKey();
                    }
                    $permissionObject = app(config('laravolt.epicentrum.models.permission'))->firstOrCreate(['name' => $permission]);

                    return $permissionObject->getKey();
                }
                if ($permission instanceof Model) {
                    return $permission->getKey();
                }
            }
        )->filter(
            function ($id) {
                if (is_int($id)) {
                    return $id > 0;
                }

                if (is_string($id)) {
                    return trim($id) !== '';
                }

                return false;
            }
        );

        $changes = $this->permissions()->sync($ids->toArray());

        if ($this->hasSyncChanges($changes)) {
            $this->unsetRelation('permissions');
            $this->invalidateAssignedUsersAccessControl();
        }

        return $changes;
    }

    protected function hasSyncChanges(array $changes): bool
    {
        return collect($changes)->flatten()->isNotEmpty();
    }

    protected function invalidateAssignedUsersAccessControl(): void
    {
        app(AccessControlInvalidator::class)->invalidateUsers($this->users()->cursor());
    }

    protected function _hasPermission($permission)
    {
        // ⚡ Bolt: Fast-path for checking permissions without instantiating models
        // if the permissions are eager-loaded
        if ($this->relationLoaded('permissions')) {
            if ($permission instanceof Model) {
                return $this->permissions->contains($permission->getKeyName(), $permission->getKey());
            }

            if (is_int($permission)) {
                return $this->permissions->contains('id', $permission);
            }

            if (is_string($permission)) {
                $permissionModel = app(config('laravolt.epicentrum.models.permission'));
                $keyType = $permissionModel->getKeyType();
                if ($keyType === 'string' && Str::isUlid($permission)) {
                    // Try to match key first, fallback to name
                    return $this->permissions->containsStrict($permissionModel->getKeyName(), $permission)
                        || $this->permissions->containsStrict('name', $permission);
                }

                return $this->permissions->containsStrict('name', $permission);
            }
        }

        $model = $permission;
        $permissionModel = app(config('laravolt.epicentrum.models.permission'));

        if (! $model instanceof Model) {
            if (is_int($permission)) {
                $model = $permissionModel->find($permission);
            } elseif (is_string($permission)) {
                $model = match ($permissionModel->getKeyType()) {
                    'int' => $permissionModel->where('name', $permission)->first(),
                    'string' => $permissionModel->whereKey($permission)->orWhere('name', $permission)->first(),
                };
            }
        }

        if (! $model instanceof Model) {
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
