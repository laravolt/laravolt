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
                $permissionNames = $this->permissions();

                // ⚡ Bolt: Prevent N+1 queries by pre-fetching existing permissions
                $existingPermissions = app(config('laravolt.epicentrum.models.permission'))
                    ->whereIn('name', $permissionNames)
                    ->get()
                    ->keyBy(fn($item) => strtolower($item->name));

                foreach ($permissionNames as $name) {
                    $permission = $existingPermissions->get(strtolower($name));
                    $status = 'No Change';

                    if (! $permission) {
                        // ⚡ Bolt: Insert fallback
                        $permission = app(config('laravolt.epicentrum.models.permission'))->firstOrCreate(['name' => $name]);
                        $status = 'New';
                    }

                    $items->push(['id' => $permission->getKey(), 'name' => $name, 'status' => $status]);
                }

                // delete unused permissions
                // ⚡ Bolt: Fix array collision by appending instead of union on numerically indexed array
                $permissionsToKeep = $permissionNames;
                $permissionsToKeep[] = '*';

                $unusedPermissions = app(config('laravolt.epicentrum.models.permission'))
                    ->whereNotIn('name', $permissionsToKeep)
                    ->get();

                foreach ($unusedPermissions as $permission) {
                    $items->push(['id' => $permission->getKey(), 'name' => $permission->name, 'status' => 'Deleted']);
                    // ⚡ Bolt: Keep individual deletion to trigger Eloquent events and avoid stale ACL cache issues
                    $permission->delete();
                }

                $items = $items->sortBy('name');

                return $items;
            }
        );
    }
}
