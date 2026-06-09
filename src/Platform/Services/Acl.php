<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Acl
{
    /**
     * All of the registered permissions.
     *
     * @var array
     */
    protected $permissions = [];

    public function permissions(): array
    {
        return $this->permissions;
    }

    public function clearPermissions(): self
    {
        $this->permissions = [];

        return $this;
    }

    public function registerPermission($permission): self
    {
        $this->permissions = array_unique(array_merge($this->permissions, (array) $permission));

        return $this;
    }

    public function syncPermission($refresh = false): Collection
    {
        return DB::transaction(
            function () use ($refresh) {
                if ($refresh) {
                    Schema::disableForeignKeyConstraints();
                    app(config('laravolt.epicentrum.models.permission'))->truncate();
                    Schema::enableForeignKeyConstraints();
                }

                $items = collect();
                $permissions = $this->permissions();
                $permissionModel = app(config('laravolt.epicentrum.models.permission'));

                // Bulk check existing permissions to avoid N+1 querying inside the loop
                $existingPermissions = collect();
                if (!empty($permissions)) {
                    $existingPermissions = $permissionModel->whereIn('name', $permissions)->get()->keyBy('name');
                }

                foreach ($permissions as $name) {
                    $status = 'No Change';

                    if ($existingPermissions->has($name)) {
                        $permission = $existingPermissions->get($name);
                    } else {
                        $permission = $permissionModel->newInstance(['name' => $name]);
                        $permission->save();
                        $status = 'New';
                    }

                    $items->push(['id' => $permission->getKey(), 'name' => $name, 'status' => $status]);
                }

                // delete unused permissions
                // Original logic used `$permissions + ['*']` where $permissions was an array, but standard array merge is safer
                $permissionsToDelete = array_merge($this->permissions(), ['*']);
                $unusedPermissions = $permissionModel
                    ->whereNotIn('name', $permissionsToDelete)
                    ->get();

                if ($unusedPermissions->isNotEmpty()) {
                    foreach ($unusedPermissions as $permission) {
                        $items->push(['id' => $permission->getKey(), 'name' => $permission->name, 'status' => 'Deleted']);
                        $permission->delete();
                    }
                }

                $items = $items->sortBy('name');

                return $items;
            }
        );
    }
}
