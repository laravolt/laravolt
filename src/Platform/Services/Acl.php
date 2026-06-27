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
            if ($refresh) {
                Schema::disableForeignKeyConstraints();
                app(config('laravolt.epicentrum.models.permission'))->truncate();
                Schema::enableForeignKeyConstraints();
            }

            $items = collect();

            // fetch existing permissions in bulk
            $permissionNames = $this->permissions();
            $existingPermissions = app(config('laravolt.epicentrum.models.permission'))
                ->whereIn('name', $permissionNames)
                ->get()
                ->keyBy(function ($item) {
                    return mb_strtolower($item->name);
                });

            foreach ($permissionNames as $name) {
                $lowerName = mb_strtolower($name);
                if ($existingPermissions->has($lowerName)) {
                    $permission = $existingPermissions->get($lowerName);
                    $status = 'No Change';
                } else {
                    $permission = app(config('laravolt.epicentrum.models.permission'))->firstOrCreate(['name' => $name]);
                    $status = 'New';
                }

                $items->push(['id' => $permission->getKey(), 'name' => $name, 'status' => $status]);
            }

            // delete unused permissions
            $permissions = array_merge($permissionNames, ['*']);
            $unusedPermissions = app(config('laravolt.epicentrum.models.permission'))
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
