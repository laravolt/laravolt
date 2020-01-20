<?php

declare(strict_types=1);

namespace Laravolt\Menu;

use Laravolt\Menu\Models\Menu;
use Laravolt\Menu\Enum\Permission;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(realpath(__DIR__ . '/../config/menu.php'), 'laravolt.menu');
    }

    public function boot()
    {
        $this->bootRoutes()
            ->bootMigrations()
            ->bootPermission()
            ->bootViews()
            ->loadMenuFromDatabase()
            ->bootMenu();
    }

    protected function bootMenu()
    {
        if (app()->bound('laravolt.menu')) {
            $menu = app('laravolt.menu')->system;
            $menu->add(__('Menu Manager'), url('menu-manager/menu'))
                ->data('icon', 'bars')
                ->data('permission', Permission::MANAGE_MENU)
                ->active(config('laravolt.menu.route.prefix') . '/menu/*');
        }

        return $this;
    }

    protected function bootPermission()
    {
        if (app()->bound('laravolt.acl')) {
            app('laravolt.acl')->registerPermission(Permission::toArray());
        }

        return $this;
    }

    protected function bootRoutes()
    {
        $router = $this->app['router'];
        require __DIR__ . '/../routes/web.php';

        return $this;
    }

    protected function bootViews()
    {
        $this->loadViewsFrom(realpath(__DIR__ . '/../resources/views'), 'menu');

        return $this;
    }

    protected function bootMigrations()
    {
        $path = realpath(__DIR__ . '/../database/migrations');
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom($path);
        }
        $this->publishes([
            $path => database_path('migrations'),
        ], 'migrations');

        return $this;
    }

    protected function loadMenuFromDatabase()
    {
        if (\Schema::hasTable('menu')) {
            app()['laravolt.menu.builder']->loadArray(Menu::toStructuredArray());
        }

        return $this;
    }
}
