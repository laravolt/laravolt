<?php

declare(strict_types=1);

namespace Laravolt\Support\Base;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

abstract class BaseServiceProvider extends ServiceProvider
{
    /** @var Collection */
    protected $config;

    abstract public function getIdentifier();

    public function register()
    {
        $file = $this->packagePath("config/{$this->getIdentifier()}.php");
        $this->mergeConfigFrom($file, "laravolt.{$this->getIdentifier()}");
        $this->publishes([$file => config_path("laravolt/{$this->getIdentifier()}.php")], 'config');

        $this->config = collect(config("laravolt.{$this->getIdentifier()}"));
    }

    public function boot()
    {
        $this->bootRoutes()
            ->bootViews()
            ->bootMigrations()
            ->bootTranslations();
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

        if (file_exists($viewFolder)) {
            $this->loadViewsFrom($viewFolder, $this->getIdentifier());
            $this->publishes(
                [$viewFolder => base_path("resources/views/vendor/{$this->getIdentifier()}}")],
                'views'
            );
        }

        return $this;
    }

    protected function bootMigrations()
    {
        $databaseFolder = $this->packagePath('database/migrations');
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom($databaseFolder);
        }

        if (file_exists($databaseFolder)) {
            $this->publishes([
                $databaseFolder => database_path('migrations'),
            ], 'migrations');
        }

        return $this;
    }

    protected function bootTranslations()
    {
        $this->loadTranslationsFrom($this->packagePath('resources/lang'), $this->getIdentifier());

        return $this;
    }

    protected function packagePath($path)
    {
        $rc = new \ReflectionClass(get_class($this));
        $dir = dirname($rc->getFileName());

        return sprintf('%s/../%s', $dir, $path);
    }
}
