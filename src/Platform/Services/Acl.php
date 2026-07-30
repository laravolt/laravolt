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
        return DB::transaction(function () use ($refresh) {
            $permissionModel = app(config('laravolt.epicentrum.models.permission'));

            if ($refresh) {
                Schema::disableForeignKeyConstraints();
                $permissionModel->truncate();
                Schema::enableForeignKeyConstraints();
            }

            $items = collect();
            $permissionNames = $this->permissions();

            // Bulk fetch existing permissions to reduce N+1 queries
            if (! empty($permissionNames)) {
                $existingPermissions = $permissionModel->whereIn('name', $permissionNames)->get()->keyBy(function ($item) {
                    return strtolower($item->name);
                });
            } else {
                $existingPermissions = collect();
            }

            // Find missing names case-insensitively
            $existingNames = $existingPermissions->pluck('name')->map(function ($name) {
                return strtolower($name);
            })->toArray();

            $missingNames = collect($permissionNames)->filter(function ($name) use ($existingNames) {
                return ! in_array(strtolower($name), $existingNames);
            })->values();

            // Insert missing permissions via firstOrCreate to preserve events and return IDs
            foreach ($missingNames as $name) {
                $permission = $permissionModel->firstOrCreate(['name' => $name]);
                $existingPermissions->put(strtolower($name), $permission);
                $items->push(['id' => $permission->getKey(), 'name' => $name, 'status' => 'New']);
            }

            // Add existing permissions that were not newly created
            foreach ($permissionNames as $name) {
                if (! $missingNames->containsStrict($name)) {
                    $permission = $existingPermissions->get(strtolower($name));
                    $items->push(['id' => $permission->getKey(), 'name' => $name, 'status' => 'No Change']);
                }
            }

            // delete unused permissions
            $permissions = $this->permissions();
            $permissions[] = '*';
            $unusedPermissions = $permissionModel
                ->whereNotIn('name', $permissions)
                ->get();

            foreach ($unusedPermissions as $permission) {
                $items->push(['id' => $permission->getKey(), 'name' => $permission->name, 'status' => 'Deleted']);
                $permission->delete();
            }

            $items = $items->sortBy('name');

            return $items;
        });
    }
}
