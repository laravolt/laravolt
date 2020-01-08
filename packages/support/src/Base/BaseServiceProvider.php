<?php

declare(strict_types=1);

namespace Laravolt\Support\Base;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class BaseServiceProvider extends ServiceProvider
{
    protected $name;

    /** @var Collection */
    protected $config;

    public function register()
    {
        $file = $this->packagePath("config/{$this->name}.php");
        $this->mergeConfigFrom($file, "laravolt.{$this->name}");
        $this->publishes([$file => config_path("laravolt/{$this->name}.php")], 'config');

        $this->config = collect(config("laravolt.{$this->name}"));
    }

    public function boot()
    {
        $this->bootRoutes()
            ->bootMigrations()
            ->bootViews();
    }

    protected function bootRoutes()
    {
        if (Arr::get($this->config, 'route.enabled')) {
            $router = $this->app['router'];
            require $this->packagePath('routes/web.php');
        }

        return $this;
    }

    protected function bootViews()
    {
        $viewFolder = $this->packagePath('resources/views');
        $this->loadViewsFrom($viewFolder, $this->name);
        $this->publishes(
            [$viewFolder => base_path("resources/views/vendor/{$this->name}}")],
            'views'
        );

        return $this;
    }

    protected function bootMigrations()
    {
        $databaseFolder = $this->packagePath('database/migrations');
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom($databaseFolder);
        }
        $this->publishes([
            $databaseFolder => database_path('migrations'),
        ], 'migrations');

        return $this;
    }

    protected function packagePath($path)
    {
        $rc = new \ReflectionClass(get_class($this));
        $dir = dirname($rc->getFileName());

        return sprintf('%s/../%s', $dir, $path);
    }
}
