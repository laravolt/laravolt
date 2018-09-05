<?php

namespace Laravolt\Epilog;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class PackageServiceProvider
 *
 * @package Laravolt\Comma
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
    }

    /**
     * Application is booting
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfigurations();
        $this->loadTranslationsFrom($this->packagePath('resources/lang'), 'cockpit');
        $this->loadViewsFrom($this->packagePath('resources/views'), 'cockpit');

        if (!$this->app->routesAreCached() && config('laravolt.cockpit.route.enabled')) {
            $this->registerRoutes();
        }

        if (config('laravolt.cockpit.menu.enabled')) {
            $this->registerMenu();
        }

        if ($this->app->bound('laravolt.acl')) {
            $this->app['laravolt.acl']->registerPermission(Permission::values());
        }
    }

    /**
     * Register the package configurations
     *
     * @return void
     */
    protected function registerConfigurations()
    {
        $this->mergeConfigFrom(
            $this->packagePath(
                'config/config.php'
            ),
            'laravolt.cockpit'
        );
        $this->publishes([
            $this->packagePath('config/config.php') => config_path('laravolt/cockpit.php'),
        ], 'config');
    }

    protected function registerMenu()
    {
        if ($this->app->bound('laravolt.sidebar')) {
            $this->systemMenu()->add('Log Viewer')->data('icon', 'file text outline');
        }
    }

    protected function registerRoutes()
    {
        $router = $this->app['router'];
        require $this->packagePath('routes/web.php');
    }

    /**
     * Loads a path relative to the package base directory
     *
     * @param string $path
     * @return string
     */
    protected function packagePath($path = '')
    {
        return sprintf("%s/../%s", __DIR__, $path);
    }

    protected function systemMenu()
    {
        $menu = $this->app['laravolt.sidebar']->system;
        if (!$menu) {
            $menu = $this->app['laravolt.sidebar']->add('System');
        }

        return $menu;
    }

}
