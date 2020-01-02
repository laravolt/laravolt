<?php

namespace Laravolt\Suitable;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class PackageServiceProvider
 *
 * @package Laravolt\Suitable
 * @see http://laravel.com/docs/master/packages#service-providers
 * @see http://laravel.com/docs/master/providers
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register the service provider.
     *
     * @see http://laravel.com/docs/master/providers#the-register-method
     * @return void
     */
    public function register()
    {
        $this->app->bind('laravolt.suitable', function ($app) {
            return new Builder();
        });
    }

    /**
     * Application is booting
     *
     * @see http://laravel.com/docs/master/providers#the-boot-method
     * @return void
     */
    public function boot()
    {
        $this->registerViews();
        $this->registerConfigurations();
        $this->loadTranslationsFrom(realpath(__DIR__.'/../resources/lang'), 'suitable');
        $this->registerMacro();
    }

    /**
     * Register the package views
     *
     * @see http://laravel.com/docs/master/packages#views
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
     * Register the package configurations
     *
     * @see http://laravel.com/docs/master/packages#configuration
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
     * Flexible where like search
     *
     * @see https://murze.be/searching-models-using-a-where-like-query-in-laravel
     * @return void
     */
    protected function registerMacro()
    {
        EloquentBuilder::macro('whereLike', function ($attributes, ?string $searchTerm) {

            if ($searchTerm === null) {
                return $this;
            }

            $searchTerm = strtolower($searchTerm);
            $this->where(function (EloquentBuilder $query) use ($attributes, $searchTerm) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        Str::contains($attribute, '.'),
                        function (EloquentBuilder $query) use ($attribute, $searchTerm) {
                            [$relationName, $relationAttribute] = explode('.', $attribute);

                            $query->orWhereHas($relationName,
                                function (EloquentBuilder $query) use ($relationAttribute, $searchTerm) {
                                    $query->whereRaw(sprintf("LOWER(%s) LIKE '%%%s%%'", $relationAttribute, $searchTerm));
                                });
                        },
                        function (EloquentBuilder $query) use ($attribute, $searchTerm) {
                            $table = $query->getModel()->getTable();
                            if (Str::contains($attribute, '->')) {
                                $query->orWhere($attribute, 'like', "%$searchTerm%");
                            } else {
                                $query->orWhereRaw(sprintf("LOWER(%s.%s) LIKE '%%%s%%'", $table, $attribute, $searchTerm));
                            }
                        }
                    );
                }
            });

            return $this;
        });
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
