<?php

namespace Laravolt\SemanticForm;

use Laravolt\SemanticForm\SemanticForm;
use Laravolt\SemanticForm\ErrorStore\IlluminateErrorStore;
use Laravolt\SemanticForm\OldInput\IlluminateOldInputProvider;
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

            $builder = new SemanticForm();
            $builder->setToken($app['session.store']->token());
            $builder->setErrorStore(new IlluminateErrorStore($app['session.store']));
            $builder->setOldInputProvider(new IlluminateOldInputProvider($app['session.store']));

            return $builder;
        });
    }

    /**
     * Application is booting
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(realpath(__DIR__.'/../resources/views/'), 'semantic-form');
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
