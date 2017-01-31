<?php

namespace Laravolt\Comma;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class PackageServiceProvider
 * @package Laravolt\Comma
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
        $this->app->singleton('laravolt.comma', function () {
            return new Comma();
        });
    }

    /**
     * Application is booting
     * @return void
     */
    public function boot()
    {

        $this->registerMigrations();
        $this->registerConfigurations();

        if (!$this->app->routesAreCached() && config('laravolt.comma.routes')) {
            $this->registerRoutes();
        }
    }

    /**
     * Register the package migrations
     * @return void
     */
    protected function registerMigrations()
    {
        if (version_compare($this->app->version(), '5.3.0', '>=')) {
            $this->loadMigrationsFrom($this->packagePath('database/migrations'));
        } else {
            $this->publishes([
                $this->packagePath('database/migrations') => database_path('/migrations'),
            ], 'migrations');
        }
    }

    /**
     * Register the package configurations
     * @return void
     */
    protected function registerConfigurations()
    {
        $this->mergeConfigFrom(
            $this->packagePath('config/config.php'), 'comma'
        );
        $this->publishes([
            $this->packagePath('config/config.php') => config_path('laravolt/comma.php'),
        ], 'config');
    }

    /**
     * Loads a path relative to the package base directory
     * @param string $path
     * @return string
     */
    protected function packagePath($path = '')
    {
        return sprintf("%s/../%s", __DIR__, $path);
    }
}
