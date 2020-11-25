<?php

namespace Laravolt\FileManager;

use Hashids\Hashids;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class PackageServiceProvider.
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('laravolt.file-manager', function ($app) {
            $hashids = new Hashids(config('app.key'));

            return new FileManager($hashids);
        });
    }

    /**
     * Application is booting.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom($this->packagePath('resources/lang'), 'file-manager');
        $this->loadViewsFrom($this->packagePath('resources/views'), 'file-manager');
        $this->registerConfigurations();
        $this->registerRoutes();
    }

    /**
     * Register the package configurations.
     *
     * @return void
     */
    protected function registerConfigurations()
    {
        $this->mergeConfigFrom(
            $this->packagePath('config/file-manager.php'),
            'laravolt.file-manager'
        );
        $this->publishes([
            $this->packagePath('config/file-manager.php') => config_path('laravolt/file-manager.php'),
        ], 'config');
    }

    protected function registerRoutes()
    {
        $router = $this->app['router'];
        require $this->packagePath('routes/web.php');
    }

    /**
     * Loads a path relative to the package base directory.
     *
     * @param string $path
     * @return string
     */
    protected function packagePath($path = '')
    {
        return sprintf("%s/../%s", __DIR__, $path);
    }
}
