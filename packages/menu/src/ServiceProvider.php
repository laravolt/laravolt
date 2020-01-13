<?php

declare(strict_types=1);

namespace Laravolt\Menu;

use Laravolt\Menu\Enum\Permission;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(realpath(__DIR__ . '/../config/menu-manager.php'), 'laravolt.menu-manager');
    }

    public function boot()
    {
        $this->bootRoutes()
            ->bootMigrations()
            ->bootPermission()
            ->bootViews()
            ->bootMenu();
    }

    protected function bootMenu()
    {
        $menu = app('laravolt.menu')->system;
        $menu->add(__('Menu Manager'), url('menu-manager/menu'))
            ->data('icon', 'bars')
            ->data('permission', Permission::MANAGE_MENU)
            ->active(config('laravolt.menu-manager.route.prefix') . '/menu/*');

        return $this;
    }

    protected function bootPermission()
    {
        $this->app['laravolt.acl']->registerPermission(Permission::toArray());

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
        $this->loadViewsFrom(realpath(__DIR__ . '/../resources/views'), 'menu-manager');

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
}
