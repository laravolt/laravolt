<?php

namespace Laravolt\Suitable;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class PackageServiceProvider.
 *
 * @see http://laravel.com/docs/master/packages#service-providers
 * @see http://laravel.com/docs/master/providers
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register the service provider.
     *
     * @see http://laravel.com/docs/master/providers#the-register-method
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('laravolt.suitable', function ($app) {
            return new Builder();
        });
    }

    /**
     * Application is booting.
     *
     * @see http://laravel.com/docs/master/providers#the-boot-method
     *
     * @return void
     */
    public function boot()
    {
        $this->registerViews();
        $this->registerConfigurations();
        $this->loadTranslationsFrom(realpath(__DIR__.'/../resources/lang'), 'suitable');
    }

    /**
     * Register the package views.
     *
     * @see http://laravel.com/docs/master/packages#views
     *
     * @return void
     */
    protected function registerViews()
    {
        // register views within the application with the set namespace
        $this->loadViewsFrom($this->packagePath('resources/views'), 'suitable');

        // allow views to be published to the storage directory
        $this->publishes([
            $this->packagePath('resources/views') => base_path('resources/views/vendor/suitable'),
        ], 'views');
    }

    /**
     * Register the package configurations.
     *
     * @see http://laravel.com/docs/master/packages#configuration
     *
     * @return void
     */
    protected function registerConfigurations()
    {
        $this->mergeConfigFrom(
            $this->packagePath('config/config.php'), 'suitable'
        );

        $this->publishes([
            $this->packagePath('config/config.php') => config_path('laravolt/suitable.php'),
        ], 'config');
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
}
