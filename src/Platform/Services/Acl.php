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

                // Optimization: Fetch existing permissions in bulk to prevent N+1 SELECT queries
                $existingPermissions = $permissionModel->whereIn('name', $this->permissions())
                    ->get()
                    ->keyBy('name');

                foreach ($this->permissions() as $name) {
                    $status = 'No Change';
                    $permission = $existingPermissions->get($name);

                    if (! $permission) {
                        // Create missing permission
                        $permission = $permissionModel->create(['name' => $name]);
                        // Add to existing permissions to avoid duplicate inserts if array contains duplicates
                        $existingPermissions->put($name, $permission);
                        $status = 'New';
                    }

                    $items->push(['id' => $permission->getKey(), 'name' => $name, 'status' => $status]);
                }

                // delete unused permissions
                // Bug fix: array_merge properly appends '*' instead of the '+' operator on indexed arrays
                $permissions = array_merge($this->permissions(), ['*']);
                $unusedPermissions = $permissionModel->whereNotIn('name', $permissions)->get();

                if ($unusedPermissions->isNotEmpty()) {
                    foreach ($unusedPermissions as $permission) {
                        $items->push(['id' => $permission->getKey(), 'name' => $permission->name, 'status' => 'Deleted']);
                        // We iterate delete() here instead of bulk delete to ensure Model Events (like caching invalidation) fire
                        $permission->delete();
                    }
                }

                $items = $items->sortBy('name');

                return $items;
            }
        );
    }
}
