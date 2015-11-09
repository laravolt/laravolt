<?php

namespace Laravolt\SemanticForm;

use AdamWathan\Form\FormBuilder;
use AdamWathan\Form\ErrorStore\IlluminateErrorStore;
use AdamWathan\Form\OldInput\IlluminateOldInputProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class PackageServiceProvider
 *
 * @package Laravolt\SemanticForm
 * @see http://laravel.com/docs/5.1/packages#service-providers
 * @see http://laravel.com/docs/5.1/providers
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @see http://laravel.com/docs/5.1/providers#deferred-providers
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @see http://laravel.com/docs/5.1/providers#the-register-method
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('semantic-form', function ($app) {

            $builder = new FormBuilder();
            $builder->setToken($app['session.store']->token());
            $builder->setErrorStore(new IlluminateErrorStore($app['session.store']));
            $builder->setOldInputProvider(new IlluminateOldInputProvider($app['session.store']));

            return new SemanticForm($builder, $app['translator']);
        });
    }

    /**
     * Application is booting
     *
     * @see http://laravel.com/docs/5.1/providers#the-boot-method
     * @return void
     */
    public function boot()
    {
        $this->registerConfigurations();
    }

    /**
     * Register the package configurations
     *
     * @see http://laravel.com/docs/5.1/packages#configuration
     * @return void
     */
    protected function registerConfigurations()
    {
        $this->mergeConfigFrom(
            $this->packagePath('config/config.php'), 'semantic-form'
        );
        $this->publishes([
            $this->packagePath('config/config.php') => config_path('semantic-form.php'),
        ], 'config');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['semantic-form'];
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
}
