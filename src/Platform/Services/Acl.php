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

                // ⚡ Bolt: Bulk fetch existing permissions to prevent N+1 queries during sync
                $existingPermissions = app(config('laravolt.epicentrum.models.permission'))
                    ->whereIn('name', $this->permissions())
                    ->get()
                    ->keyBy(fn ($item) => strtolower((string) $item->name));

                foreach ($this->permissions() as $name) {
                    $status = 'No Change';
                    $key = strtolower((string) $name);

                    if ($existingPermissions->has($key)) {
                        $permission = $existingPermissions->get($key);
                    } else {
                        $permission = app(config('laravolt.epicentrum.models.permission'))->firstOrCreate(['name' => $name]);
                        $status = 'New';
                    }

                    $items->push(['id' => $permission->getKey(), 'name' => $name, 'status' => $status]);
                }

                // delete unused permissions
                // Fix: avoid array union bug which ignores values if index exists on the left
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
