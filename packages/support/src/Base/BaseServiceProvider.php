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

    protected function enabled()
    {
        return config('laravolt.platform.features.'.$this->getIdentifier()) ?? true;
    }

    public function register()
    {
        if ($this->enabled()) {
            $file = $this->packagePath("config/{$this->getIdentifier()}.php");
            if (file_exists($file)) {
                $this->mergeConfigFrom($file, "laravolt.{$this->getIdentifier()}");
                $this->publishes(
                    [$file => config_path("laravolt/{$this->getIdentifier()}.php")],
                    ['config', 'laravolt-config']
                );
            }

            $this->config = collect(config("laravolt.{$this->getIdentifier()}"));
        }
    }

    public function boot()
    {
        if ($this->enabled()) {
            $this->bootRoutes()
                ->bootMenu()
                ->bootViews()
                ->bootMigrations()
                ->bootTranslations();
        }
    }

    protected function bootRoutes()
    {
        if (Arr::get($this->config, 'routes.enabled') && ! $this->app->routesAreCached()) {
            $router = $this->app['router'];
            require $this->packagePath('routes/web.php');
        }

        return $this;
    }

    protected function bootMenu()
    {
        if (Arr::get($this->config, 'menu.enabled')) {
            if (method_exists($this, 'menu')) {
                $this->menu();
            }
        }

        return $this;
    }

    protected function bootViews()
    {
        $viewFolder = $this->packagePath('resources/views');

        if (file_exists($viewFolder)) {
            $this->loadViewsFrom($viewFolder, $this->getIdentifier());
            $this->publishes(
                [$viewFolder => base_path("resources/views/vendor/{$this->getIdentifier()}")],
                'views'
            );
        }

        return $this;
    }

    protected function bootMigrations()
    {
        $databaseFolder = $this->packagePath('database/migrations');

        if (file_exists($databaseFolder)) {
            $this->publishes([
                $databaseFolder => database_path('migrations'),
            ], 'laravolt-migrations');
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
