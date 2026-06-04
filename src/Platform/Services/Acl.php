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
                $permissionsList = $this->permissions();

                // ⚡ Bolt Optimization: Pre-fetch all requested permissions in a single query to avoid N+1 issues
                // Key the fetched collection by lowercase name to ensure case-insensitive matching in memory
                $existingPermissions = app(config('laravolt.epicentrum.models.permission'))
                    ->whereIn('name', $permissionsList)
                    ->get()
                    ->keyBy(fn ($item) => strtolower($item->name));

                foreach ($permissionsList as $name) {
                    $lowerName = strtolower($name);
                    $status = 'No Change';

                    if ($existingPermissions->has($lowerName)) {
                        $permission = $existingPermissions->get($lowerName);
                    } else {
                        $permission = app(config('laravolt.epicentrum.models.permission'))->create(['name' => $name]);
                        $status = 'New';
                    }

                    $items->push(['id' => $permission->getKey(), 'name' => $name, 'status' => $status]);
                }

                // delete unused permissions
                // Fix: avoid using array union operator `+` on numerically indexed arrays as it drops values
                $permissions = array_merge($permissionsList, ['*']);
                $unusedPermissions = app(config('laravolt.epicentrum.models.permission'))
                    ->whereNotIn('name', $permissions)
                    ->get();

                foreach ($unusedPermissions as $permission) {
                    $items->push(['id' => $permission->getKey(), 'name' => $permission->name, 'status' => 'Deleted']);
                    $permission->delete();
                }

                $items = $items->sortBy('name');

                return $items;
            }
        );
    }
}
