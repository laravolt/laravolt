<?php

declare(strict_types=1);

namespace Laravolt\Lookup;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $file = realpath(__DIR__ . '/../config/lookup.php');
        $this->mergeConfigFrom($file, 'laravolt.lookup');
        $this->publishes([$file => config_path('laravolt/lookup.php')], 'config');
    }

    public function boot()
    {
        $this->bootRoutes()
            ->bootMigrations()
            ->bootViews()
            ->bootMenu();
    }

    protected function bootMenu()
    {
        if ($this->app->bound('laravolt.menu') && config('laravolt.lookup.menu.enabled')) {
            $menu = app('laravolt.menu')->system;
            $group = $menu->add(__('Lookup'))
                ->data('icon', 'list')
                ->data('permission', config('laravolt.lookup.permission'));
            foreach (config('laravolt.lookup.collections') as $key => $collection) {
                $menu = $group->add($collection['label'], url("lookup/{$key}"))
                    ->active('lookup/' . $key . '/*');
                foreach ($collection['data'] ?? [] as $dataKey => $dataValue) {
                    $menu->data($dataKey, $dataValue);
                }
            }
        }

        return $this;
    }

    protected function bootRoutes()
    {
        if (config('laravolt.lookup.route.enabled')) {
            $router = $this->app['router'];
            require __DIR__ . '/../routes/web.php';
        }

        return $this;
    }

    protected function bootViews()
    {
        $this->loadViewsFrom(realpath(__DIR__ . '/../resources/views'), 'lookup');
        $this->publishes(
            [realpath(__DIR__ . '/../resources/views') => base_path('resources/views/vendor/lookup')],
            'views'
        );

        return $this;
    }

    protected function bootMigrations()
    {
        $path = realpath(__DIR__ . '/../database/migrations');
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom($path);
        }
        $this->publishes([
            $path => database_path('migrations'),
        ], 'migrations');

        return $this;
    }
}
