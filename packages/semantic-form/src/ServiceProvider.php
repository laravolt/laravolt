<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravolt\PrelineForm\ErrorStore\IlluminateErrorStore as PrelineIlluminateErrorStore;
use Laravolt\PrelineForm\OldInput\IlluminateOldInputProvider as PrelineInputIlluminateOldInputProvider;
use Laravolt\PrelineForm\PrelineForm;
use Laravolt\SemanticForm\ErrorStore\IlluminateErrorStore;
use Laravolt\SemanticForm\OldInput\IlluminateOldInputProvider;

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
        $this->app->singleton('semantic-form', function ($app) {
            $builder = new SemanticForm($app['config']->get('laravolt.ui'));
            $builder->setErrorStore(new IlluminateErrorStore($app['session.store']));
            $builder->setOldInputProvider(new IlluminateOldInputProvider($app['session.store']));

            return $builder;
        });

        $this->app->singleton('preline-form', function ($app) {
            $builder = new PrelineForm($app['config']->get('laravolt.ui'));
            $builder->setErrorStore(new PrelineIlluminateErrorStore($app['session.store']));
            $builder->setOldInputProvider(new PrelineInputIlluminateOldInputProvider($app['session.store']));

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
        $this->loadViewsFrom(realpath(__DIR__.'/../resources/views/'), 'semantic-form');
        require __DIR__.'/../routes/web.php';
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
