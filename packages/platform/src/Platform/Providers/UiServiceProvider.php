<?php

declare(strict_types=1);

namespace Laravolt\Platform\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravolt\Platform\Http\Middleware\FlashMiddleware;
use Laravolt\Platform\Services\Flash;
use Laravolt\Platform\Services\Menu;
use Laravolt\Platform\Services\MenuBuilder;
use Lavary\Menu\Builder;
use Stolz\Assets\Manager;

/**
 * Class PackageServiceProvider.
 *
 * @see     http://laravel.com/docs/master/packages#service-providers
 * @see     http://laravel.com/docs/master/providers
 */
class UiServiceProvider extends BaseServiceProvider
{
    /**
     * Register the service provider.
     *
     * @see    http://laravel.com/docs/master/providers#the-register-method
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('laravolt.menu.sidebar', function () {
            return new Menu();
        });

        $this->app->singleton(
            'laravolt.menu',
            function (Application $app) {
                return app('laravolt.menu.sidebar')->make(
                    'sidebar',
                    function (Builder $menu) {
                        return $menu;
                    }
                );
            }
        );

        $this->bootConfig();

        // We add default menu in register() method,
        // to make sure it is always accessible by other providers.
        $this->app['laravolt.menu']
            ->add('System')
            ->data('order', config('laravolt.ui.system_menu.order'));

        $this->registerAssets();

        $this->registerMenuBuilder();

        $this->registerFlash();
    }

    /**
     * Application is booting.
     *
     * @see    http://laravel.com/docs/master/providers#the-boot-method
     *
     * @return void
     */
    public function boot()
    {
        $this
            ->bootViews()
            ->buildMenuFromConfig();

        if (!$this->app->runningInConsole() && !class_exists('Laravolt\Ui\ServiceProvider')) {
            $this->app['router']->pushMiddlewareToGroup('web', FlashMiddleware::class);
        }
    }

    protected function bootConfig()
    {
        $this->mergeConfigFrom(
            platform_path('config/ui.php'),
            'laravolt.ui'
        );

        $this->publishes(
            [
                platform_path('config/ui.php') => config_path('laravolt/ui.php'),
                platform_path('config/menu.php') => config_path('laravolt/menu.php'),
            ]
        );

        $theme = $this->app['config']->get('laravolt.ui.sidebar_theme');
        $themeOptions = $this->app['config']->get('laravolt.ui.themes.'.$theme);
        $this->app['config']->set('laravolt.ui.options', $themeOptions);

        return $this;
    }

    /**
     * Register the package views.
     *
     * @see    http://laravel.com/docs/master/packages#views
     *
     * @return void
     */
    protected function bootViews()
    {
        // register views within the application with the set namespace
        $this->loadViewsFrom(platform_path('resources/views'), 'ui');

        Paginator::defaultView('laravolt::pagination.default');
        Paginator::defaultSimpleView('laravolt::pagination.simple');

        return $this;
    }

    protected function registerFlash()
    {
        $this->app->singleton('laravolt.flash', function (Application $app) {
            return $app->make(Flash::class);
        });

        $this->app->singleton('laravolt.flash.middleware', function (Application $app) {
            return new FlashMiddleware($app['laravolt.flash']);
        });
    }

    protected function registerMenuBuilder()
    {
        $this->app->singleton('laravolt.menu.builder', function (Application $app) {
            return $app->make(MenuBuilder::class);
        });
    }

    protected function buildMenuFromConfig()
    {
        $this->app['laravolt.menu.builder']->loadArray(config('laravolt.menu') ?? []);

        return $this;
    }

    protected function registerAssets()
    {
        if (!$this->app->bound('stolz.assets.group.laravolt')) {
            $this->app->singleton('stolz.assets.group.laravolt', function () {
                return new Manager([
                    'public_dir' => public_path('laravolt'),
                    'css_dir' => '',
                    'js_dir' => '',
                ]);
            });
        }

        \Stolz\Assets\Laravel\Facade::group('laravolt')
            ->registerCollection(
                'vegas',
                [
                    'laravolt/plugins/vegas/vegas.min.css',
                    'laravolt/plugins/vegas/vegas.min.js',
                ]
            )->registerCollection(
                'autoNumeric',
                [
                    'laravolt/plugins/autoNumeric.min.js',
                ]
            );
    }
}
