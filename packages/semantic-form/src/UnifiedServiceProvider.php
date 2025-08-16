<?php

namespace Laravolt\SemanticForm;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Unified Service Provider for Form Builders
 * 
 * This service provider handles registration of both SemanticForm and PrelineForm
 * builders, along with the FormManager that handles switching between them.
 */
class UnifiedServiceProvider extends BaseServiceProvider
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
        // Merge form configuration
        $this->mergeConfigFrom(__DIR__.'/../config/form.php', 'form');

        // Register the form manager
        $this->app->singleton('form-manager', function ($app) {
            return new FormManager($app);
        });

        // Register the UI manager
        $this->app->singleton('ui-manager', function ($app) {
            return new UIManager($app);
        });

        // Register individual form builders for direct access if needed
        $this->registerSemanticForm();
        $this->registerPrelineForm();

        // Register unified facade alias
        $this->app->alias('form-manager', 'form');
        $this->app->alias('ui-manager', 'ui');
    }

    /**
     * Register SemanticForm builder.
     *
     * @return void
     */
    protected function registerSemanticForm()
    {
        $this->app->singleton('semantic-form', function ($app) {
            $builder = new SemanticForm($app['config']->get('laravolt.ui', []));
            $builder->setErrorStore(new ErrorStore\IlluminateErrorStore($app['session.store']));
            $builder->setOldInputProvider(new OldInput\IlluminateOldInputProvider($app['session.store']));

            return $builder;
        });
    }

    /**
     * Register PrelineForm builder if available.
     *
     * @return void
     */
    protected function registerPrelineForm()
    {
        if (class_exists(\Laravolt\PrelineForm\PrelineForm::class)) {
            $this->app->singleton('preline-form', function ($app) {
                $builder = new \Laravolt\PrelineForm\PrelineForm($app['config']->get('laravolt.ui', []));
                $builder->setErrorStore(new \Laravolt\PrelineForm\ErrorStore\IlluminateErrorStore($app['session.store']));
                $builder->setOldInputProvider(new \Laravolt\PrelineForm\OldInput\IlluminateOldInputProvider($app['session.store']));

                return $builder;
            });
        }
    }

    /**
     * Application is booting.
     *
     * @return void
     */
    public function boot()
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/form.php' => config_path('form.php'),
        ], 'form-config');

        $this->publishes([
            __DIR__.'/../../config/ui.php' => config_path('ui.php'),
        ], 'ui-config');

        // Load views for both form builders
        $this->loadViewsFrom(realpath(__DIR__.'/../resources/views/'), 'semantic-form');
        
        if (is_dir(realpath(__DIR__.'/../../preline-form/resources/views/'))) {
            $this->loadViewsFrom(realpath(__DIR__.'/../../preline-form/resources/views/'), 'preline-form');
        }

        // Load routes
        if (file_exists(__DIR__.'/../routes/web.php')) {
            require __DIR__.'/../routes/web.php';
        }

        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Laravolt\SemanticForm\Console\FormBuilderCommand::class,
                \Laravolt\SemanticForm\Console\UIFrameworkCommand::class,
            ]);
        }

        // Auto-detect form builder if enabled
        if (config('form.auto_detect.enabled')) {
            $manager = $this->app['form-manager'];
            $manager->autoDetect();
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['form-manager', 'form', 'ui-manager', 'ui', 'semantic-form', 'preline-form'];
    }
}