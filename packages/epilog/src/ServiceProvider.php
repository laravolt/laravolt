<?php

namespace Laravolt\Epilog;

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
    }

    /**
     * Application is booting.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfigurations();
        $this->loadTranslationsFrom($this->packagePath('resources/lang'), 'epilog');
        $this->loadViewsFrom($this->packagePath('resources/views'), 'epilog');

        if (!$this->app->routesAreCached() && config('laravolt.epilog.route.enabled')) {
            $this->registerRoutes();
        }

        if (config('laravolt.epilog.menu.enabled')) {
            $this->registerMenu();
        }

        if ($this->app->bound('laravolt.acl')) {
            $this->app['laravolt.acl']->registerPermission(Permission::toArray());
        }
    }

    /**
     * Register the package configurations.
     *
     * @return void
     */
    protected function registerConfigurations()
    {
        $this->mergeConfigFrom(
            $this->packagePath(
                'config/config.php'
            ),
            'laravolt.epilog'
        );
        $this->publishes([
            $this->packagePath('config/config.php') => config_path('laravolt/epilog.php'),
        ], 'config');
    }

    protected function registerMenu()
    {
        if ($this->app->bound('laravolt.menu')) {
            $this->systemMenu()
                ->add(__('Application Log'), route('epilog::log.index'))
                ->data('permission', Permission::VIEW_LOG)
                ->active('epilog/*')
                ->data('icon', 'file text');
        }
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
     *
     * @return string
     */
    protected function packagePath($path = '')
    {
        return sprintf('%s/../%s', __DIR__, $path);
    }

    protected function systemMenu()
    {
        $menu = $this->app['laravolt.menu']->system;
        if (!$menu) {
            $menu = $this->app['laravolt.menu']->add('System');
        }

        return $menu;
    }
}
