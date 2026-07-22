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

                // Fetch existing permissions to reduce queries
                $existing = app(config('laravolt.epicentrum.models.permission'))
                    ->whereIn('name', $permissions)
                    ->get()
                    ->keyBy(
                        function ($item) {
                            return mb_strtolower($item->name);
                        }
                    );

                foreach ($permissions as $name) {
                    $lowerName = mb_strtolower($name);
                    if ($existing->has($lowerName)) {
                        $permission = $existing->get($lowerName);
                        $items->push(['id' => $permission->getKey(), 'name' => $name, 'status' => 'No Change']);
                    } else {
                        $permission = app(config('laravolt.epicentrum.models.permission'))->create(['name' => $name]);
                        $items->push(['id' => $permission->getKey(), 'name' => $name, 'status' => 'New']);
                    }
                }

                // delete unused permissions
                $permissionsWithWildcard = array_merge($permissions, ['*']);
                $unusedPermissions = app(config('laravolt.epicentrum.models.permission'))
                    ->whereNotIn('name', $permissionsWithWildcard)
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
