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
            $permission = $this->resolvePermissionModel($permission, true);
        }

        $this->permissions()->attach($permission);
        $this->unsetRelation('permissions');
        $this->invalidateAssignedUsersAccessControl();

        return $this;
    }

    public function removePermission($permission): self
    {
        if (is_string($permission)) {
            $permission = $this->resolvePermissionModel($permission, false);
        }

        if ($permission === null) {
            return $this;
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
        // ⚡ Bolt: Prevent N+1 queries by batch-fetching permissions by name
        $names = collect($permissions)
            ->filter(fn ($p) => is_string($p) && ! is_numeric($p) && ! str($p)->isUlid())
            ->values()
            ->all();

        $resolvedNames = [];
        if (! empty($names)) {
            $permissionModel = app(config('laravolt.epicentrum.models.permission'));
            $existing = $permissionModel->whereIn('name', $names)->get();
            $existingNames = $existing->pluck('name')->map(fn ($name) => strtolower($name))->toArray();

            $existing->each(
                function ($p) use (&$resolvedNames) {
                    $resolvedNames[strtolower($p->name)] = $p->getKey();
                }
            );

            $missing = collect($names)
                ->filter(fn ($name) => ! in_array(strtolower($name), $existingNames))
                ->unique();

            $missing->each(
                function ($name) use (&$resolvedNames, $permissionModel) {
                    $resolvedNames[strtolower($name)] = $permissionModel->firstOrCreate(['name' => $name])->getKey();
                }
            );
        }

        $ids = collect($permissions)->map(
            function ($permission) use ($resolvedNames) {
                if (is_string($permission) && str($permission)->isUlid()) {
                    return $permission;
                }
                if (is_numeric($permission)) {
                    return (int) $permission;
                }
                if (is_string($permission)) {
                    return $resolvedNames[strtolower($permission)] ?? null;
                }
                if ($permission instanceof Model) {
                    return $permission->getKey();
                }

                return null;
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

    protected function resolvePermissionModel(string $permission, bool $createMissing): ?Model
    {
        $permissionModel = app(config('laravolt.epicentrum.models.permission'));

        if ($permissionModel->getKeyType() === 'string' && Str::isUlid($permission)) {
            $found = $permissionModel->whereKey($permission)->first();

            if ($found !== null) {
                return $found;
            }
        }

        return $createMissing
            ? $permissionModel->firstOrCreate(['name' => $permission])
            : $permissionModel->where('name', $permission)->first();
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
