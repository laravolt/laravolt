<?php

namespace Laravolt\Thunderclap;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * This is the service provider.
 *
 * Place the line below in the providers array inside app/config/app.php
 * <code>'Laravolt\Packer\PackerServiceProvider',</code>
 *
 * @author uyab
 *
 **/
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The console commands.
     *
     * @var bool
     */
    protected $commands = [
        'Laravolt\Thunderclap\Commands\Generator',
    ];

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfigurations();
    }

    /**
     * Register the command.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }

    /**
     * Register the package configurations.
     *
     * @see http://laravel.com/docs/5.1/packages#configuration
     *
     * @return void
     */
    protected function registerConfigurations()
    {
        $this->mergeConfigFrom(
            $this->packagePath('config/config.php'), 'laravolt.thunderclap'
        );
        $this->publishes([
            $this->packagePath('config/config.php') => config_path('laravolt/thunderclap.php'),
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
