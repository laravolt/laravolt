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

        // Register event handler for config caching
        $this->app->booting(function () {
            // Pre-process any Rule objects in configuration to make them serializable
            if ($this->app->configurationIsCached() === false) {
                $this->processAutoSchemaConfigs();
            }
        });
    }

    protected function menu()
    {
        app('laravolt.menu.builder')->register(function ($menu) {
            $menu = $menu->system;
            if ($menu) {
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
            }
        });
    }

    /**
     * Process auto-crud schema configurations to make validation rules serializable.
     * This is needed to prevent errors when running php artisan config:cache
     */
    protected function processAutoSchemaConfigs(): void
    {
        $resources = config('laravolt.auto-crud-resources');
        if ($resources === null) {
            return;
        }

        $processedResources = [];

        // For each resource in config, process it to handle non-serializable rules
        foreach ($resources as $key => $resource) {
            if (isset($resource['schema'])) {
                // Process the resource configuration to make rules serializable
                // The SchemaTransformer constructor modifies the config array
                $processedResources[$key] = (new SchemaTransformer($resource))->getProcessedConfig();
            } else {
                $processedResources[$key] = $resource;
            }
        }

        // Update the config repository with the processed resources
        config(['laravolt.auto-crud-resources' => $processedResources]);
    }
}
