<?php

declare(strict_types=1);

namespace Laravolt\Platform\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravolt\Asset\AssetManager;
use Laravolt\Platform\Services\Flash;
use Laravolt\Platform\Services\Menu;
use Laravolt\Platform\Services\MenuBuilder;

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
     * @return void
     */
    public function register()
    {
        $this->app->singleton('laravolt.menu.sidebar', function () {
            return new Menu();
        });

        $this->bootConfig();

        // We add default menu in register() method,
        // to make sure it is always accessible by other providers.
        app('laravolt.menu.sidebar')->register(function ($menu) {
            $menu->add('Modules')->data('order', config('laravolt.ui.system_menu.order'));
        });
        app('laravolt.menu.sidebar')->register(function ($menu) {
            $menu->add('System')->data('order', config('laravolt.ui.system_menu.order') + 1);
        });

        $this->registerAssets();

        $this->registerMenuBuilder();

        $this->registerFlash();
    }

    /**
     * Application is booting.
     *
     * @see    http://laravel.com/docs/master/providers#the-boot-method
     * @return void
     */
    public function boot()
    {
        $this
            ->bootViews()
            ->buildMenuFromConfig();
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
     * @return self
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

        $this->app->singleton(FlashMiddleware::class, function (Application $app) {
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
        $menuDir = base_path('menu');
        if (is_dir($menuDir)) {
            View::composer('laravolt::menu.sidebar', function () use ($menuDir) {
                foreach (new \FilesystemIterator($menuDir) as $file) {
                    $menu = include $file->getPathname();
                    $this->app['laravolt.menu.builder']->loadArray($menu);
                }
            });
        }

        return $this;
    }

    protected function registerAssets()
    {
        if (! $this->app->bound('laravolt.asset.group.laravolt')) {
            $this->app->singleton('laravolt.asset.group.laravolt', function () {
                return new AssetManager([
                    'public_dir' => public_path('laravolt'),
                    'css_dir' => '',
                    'js_dir' => '',
                ]);
            });
        }
    }
}
