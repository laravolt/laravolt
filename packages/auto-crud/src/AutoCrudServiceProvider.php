<?php

declare(strict_types=1);

namespace Laravolt\AutoCrud;

use Laravolt\AutoCrud\Tables\ResourceTable;
use Laravolt\Support\Base\BaseServiceProvider;
use Livewire\Livewire;

class AutoCrudServiceProvider extends BaseServiceProvider
{
    public function getIdentifier()
    {
        return 'auto-crud';
    }

    public function boot()
    {
        parent::boot();

        Livewire::component('laravolt::auto-crud.resource.table', ResourceTable::class);
    }

    protected function menu()
    {
        app('laravolt.menu.builder')->register(function ($menu) {
            $menu = $menu->system;
            $group = $menu->add(config('laravolt.auto-crud.menu.label'))
                ->data('icon', 'cube')
                ->data('order', 10)
                ->data('permission', config('laravolt.auto-crud.permission') ?? []);

            foreach (config('laravolt.auto-crud-resources', []) as $key => $resource) {
                $menu = $group->add($resource['label'], url("resource/{$key}"))
                    ->active('resource/'.$key.'/*');
                foreach ($resource['data'] ?? [] as $dataKey => $dataValue) {
                    $menu->data($dataKey, $dataValue);
                }
            }
        });
    }
}
