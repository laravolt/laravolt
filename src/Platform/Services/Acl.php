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

                // ⚡ Bolt: Fast-path to avoid N+1 queries when syncing permissions.
                // We do a bulk query to find existing permissions, and only execute save() for the missing ones.
                $permissionsToSync = $this->permissions();
                $existingPermissions = [];
                if (! empty($permissionsToSync)) {
                    $existingPermissions = app(config('laravolt.epicentrum.models.permission'))
                        ->whereIn('name', $permissionsToSync)
                        ->get()
                        ->keyBy(fn ($item) => strtolower($item->name))
                        ->all();
                }

                foreach ($permissionsToSync as $name) {
                    $lowerName = strtolower($name);

                    if (isset($existingPermissions[$lowerName])) {
                        $permission = $existingPermissions[$lowerName];
                        $status = 'No Change';
                    } else {
                        $permission = app(config('laravolt.epicentrum.models.permission'))->newInstance(['name' => $name]);
                        $permission->save();
                        $status = 'New';
                    }

                    $items->push(['id' => $permission->getKey(), 'name' => $name, 'status' => $status]);
                }

                // delete unused permissions
                $permissions = $this->permissions();
                $permissions[] = '*';
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
