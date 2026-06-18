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
                $permissionModel = app(config('laravolt.epicentrum.models.permission'));

                // ⚡ Bolt: Resolve N+1 query issue when checking and creating permissions
                $permissionsList = collect($this->permissions());
                $existingPermissions = $permissionModel->whereIn('name', $permissionsList)->get()->keyBy('name');

                foreach ($permissionsList as $name) {
                    $permission = $existingPermissions->get($name);
                    $status = 'No Change';

                    if (! $permission) {
                        $permission = $permissionModel->create(['name' => $name]);
                        $status = 'New';
                    }

                    $items->push(['id' => $permission->getKey(), 'name' => $name, 'status' => $status]);
                }

                // delete unused permissions
                $permissionsToKeep = $this->permissions();
                $permissionsToKeep[] = '*';
                $unusedPermissions = $permissionModel
                    ->whereNotIn('name', $permissionsToKeep)
                    ->get();

                if ($unusedPermissions->isNotEmpty()) {
                    foreach ($unusedPermissions as $permission) {
                        $items->push(['id' => $permission->getKey(), 'name' => $permission->name, 'status' => 'Deleted']);
                    }

                    // ⚡ Bolt: Batch delete unused permissions to avoid N+1 queries
                    $permissionModel->whereIn($permissionModel->getKeyName(), $unusedPermissions->modelKeys())->delete();
                }

                $items = $items->sortBy('name');

                return $items;
            }
        );
    }
}
