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
        $this->app->singleton('semantic-form', function ($app) {

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
     * @return void
     */
    public function boot()
    {

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
}
