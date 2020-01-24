<?php

namespace Laravolt\Comma;

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
        $this->app->singleton(
            'laravolt.comma', function () {
                return new Comma();
            }
        );

        $this->app->bind(
            'laravolt.comma.models.category', function () {
                return $this->app->make(config('laravolt.comma.models.category'));
            }
        );

        $this->app->bind(
            'laravolt.comma.models.post', function () {
                return $this->app->make(config('laravolt.comma.models.post'));
            }
        );

        $this->app->bind(
            'laravolt.comma.models.tag', function () {
                return $this->app->make(config('laravolt.comma.models.tag'));
            }
        );
    }

    /**
     * Application is booting.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMigrations();
        $this->registerConfigurations();
        $this->loadTranslationsFrom($this->packagePath('resources/lang'), 'comma');
        $this->loadViewsFrom($this->packagePath('resources/views'), 'comma');

        if (!$this->app->routesAreCached() && config('laravolt.comma.route.enabled')) {
            $this->registerRoutes();
        }

        if (config('laravolt.comma.menu.enabled')) {
            $this->registerMenu();
        }
    }

    /**
     * Register the package migrations.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        $this->loadMigrationsFrom($this->packagePath('database/migrations'));
    }

    /**
     * Register the package configurations.
     *
     * @return void
     */
    protected function registerConfigurations()
    {
        $this->mergeConfigFrom(
            $this->packagePath('config/config.php'), 'laravolt.comma'
        );
        $this->publishes(
            [
                $this->packagePath('config/config.php') => config_path('laravolt/comma.php'),
            ], 'config'
        );
    }

    protected function registerMenu()
    {
        if ($this->app->bound('laravolt.menu')) {
            $group = $this->app['laravolt.menu']->add('CMS');
            foreach (config('laravolt.comma.collections') as $key => $collection) {
                $menu = $group->add($collection['label'], route('comma::posts.index', ['collection' => $key]))
                    ->active('cms/posts/'.$key);
                foreach ($collection['data'] as $dataKey => $dataValue) {
                    $menu->data($dataKey, $dataValue);
                }
            }
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
}
