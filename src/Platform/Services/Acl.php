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
                $registeredNames = $this->permissions();

                // ⚡ Bolt: Batch check existing permissions to avoid N+1 queries during firstOrNew
                $existingPermissions = app(config('laravolt.epicentrum.models.permission'))
                    ->whereIn('name', $registeredNames)
                    ->get()
                    ->keyBy(fn ($item) => strtolower($item->name));

                foreach ($registeredNames as $name) {
                    $lowerName = strtolower($name);
                    if ($existingPermissions->has($lowerName)) {
                        $permission = $existingPermissions->get($lowerName);
                        $items->push(['id' => $permission->getKey(), 'name' => $name, 'status' => 'No Change']);
                    } else {
                        $permission = app(config('laravolt.epicentrum.models.permission'))->create(['name' => $name]);
                        $items->push(['id' => $permission->getKey(), 'name' => $name, 'status' => 'New']);
                    }
                }

                // delete unused permissions
                $permissions = $this->permissions() + ['*'];
                $unusedPermissions = app(config('laravolt.epicentrum.models.permission'))
                    ->whereNotIn('name', $permissions)
                    ->get();

                if ($unusedPermissions->isNotEmpty()) {
                    foreach ($unusedPermissions as $permission) {
                        $items->push(['id' => $permission->getKey(), 'name' => $permission->name, 'status' => 'Deleted']);
                    }

                    // ⚡ Bolt: Bulk delete unused permissions to avoid N+1 queries during model deletion
                    $unusedPermissions->toQuery()->delete();
                }

                $items = $items->sortBy('name');

                return $items;
            }
        );
    }
}
