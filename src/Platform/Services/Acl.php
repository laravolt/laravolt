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

                // Get all existing permissions in one query
                $permissionModel = app(config('laravolt.epicentrum.models.permission'));
                $existingPermissions = collect();
                if (!empty($permissions)) {
                    $existingPermissions = $permissionModel->whereIn('name', $permissions)->get()->keyBy('name');
                }

                $newRecords = [];
                foreach ($permissions as $name) {
                    if ($existingPermissions->has($name)) {
                        $permission = $existingPermissions->get($name);
                        $items->push(['id' => $permission->getKey(), 'name' => $name, 'status' => 'No Change']);
                    } else {
                        $newRecords[] = $name;
                    }
                }

                if (!empty($newRecords)) {
                    foreach ($newRecords as $name) {
                        $permission = $permissionModel->create(['name' => $name]);
                        $items->push(['id' => $permission->getKey(), 'name' => $name, 'status' => 'New']);
                    }
                }

                // delete unused permissions
                $allPermissions = array_merge($permissions, ['*']);
                $unusedPermissions = $permissionModel->whereNotIn('name', $allPermissions)->get();

                if ($unusedPermissions->isNotEmpty()) {
                    foreach ($unusedPermissions as $permission) {
                        $items->push(['id' => $permission->getKey(), 'name' => $permission->name, 'status' => 'Deleted']);
                        $permission->delete();
                    }
                }

                return $items->sortBy('name');
            }
        );
    }
}
