<?php

namespace Laravolt\PrelineForm;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravolt\PrelineForm\ErrorStore\IlluminateErrorStore;
use Laravolt\PrelineForm\OldInput\IlluminateOldInputProvider;

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
        $this->app->singleton('preline-form', function ($app) {
            $builder = new PrelineForm($app['config']->get('laravolt.ui'));
            $builder->setErrorStore(new IlluminateErrorStore($app['session.store']));
            $builder->setOldInputProvider(new IlluminateOldInputProvider($app['session.store']));

            return $builder;
        });
    }

    /**
     * Application is booting.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(realpath(__DIR__.'/../resources/views/'), 'preline-form');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['preline-form'];
    }
}
