<?php

declare(strict_types=1);

namespace Laravolt\Platform\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravolt\Epicentrum\Console\Commands\ManageRole;
use Laravolt\Epicentrum\Console\Commands\ManageUser;
use Laravolt\Epicentrum\Contracts\Requests\Account\Delete;
use Laravolt\Epicentrum\Contracts\Requests\Account\Store;
use Laravolt\Epicentrum\Contracts\Requests\Account\Update;
use Laravolt\Platform\Enums\Permission;
use Laravolt\Support\Contracts\TimezoneRepository;

/**
 * Class PackageServiceProvider
 */
class EpicentrumServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     * @return void
     * @throws \Exception
     */
    public function register()
    {
        $this->app->bind(
            \Laravolt\Epicentrum\Repositories\RepositoryInterface::class,
            config('laravolt.epicentrum.repository.user')
        );

        $this->app->bind(
            \Laravolt\Epicentrum\Repositories\RoleRepositoryInterface::class,
            config('laravolt.epicentrum.repository.role')
        );

        $this->app->bind(
            TimezoneRepository::class,
            config('laravolt.epicentrum.repository.timezone')
        );

        $this->app->bind('laravolt.epicentrum.role', function () {
            return app(config('laravolt.epicentrum.models.role'));
        });

        $this->app->bind('laravolt.epicentrum.permission', function () {
            return app(config('laravolt.epicentrum.models.permission'));
        });

        $this->app->bind(Store::class, config('laravolt.epicentrum.requests.account.store'));
        $this->app->bind(Update::class, config('laravolt.epicentrum.requests.account.update'));
        $this->app->bind(Delete::class, config('laravolt.epicentrum.requests.account.delete'));
    }

    /**
     * Application is booting
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(realpath(__DIR__ . '/../resources/lang'), 'epicentrum');

        if (config('laravolt.epicentrum.route.enable')) {
            $this->loadRoutes();
        }

        if (config('laravolt.epicentrum.menu.enable')) {
            $this->registerMenu();
        }

        $this->registerBlade();

        if ($this->app->bound('laravolt.acl')) {
            $this->app['laravolt.acl']->registerPermission(Permission::toArray());
        }

        if ($this->app->runningInConsole()) {
            $this->registerCommand();
        }
    }

    protected function loadRoutes()
    {
        require platform_path('routes/web.php');
    }

    protected function registerMenu()
    {
        if ($this->app->bound('laravolt.menu')) {
            $menu = app('laravolt.menu')->system;
            $menu->add(trans('laravolt::label.users'), route('epicentrum::users.index'))
                ->data('icon', 'users')
                ->data('permission', Permission::MANAGE_USER)
                ->active(config('laravolt.epicentrum.route.prefix') . '/users/*');

            $menu->add(trans('laravolt::label.roles'), route('epicentrum::roles.index'))
                ->data('icon', 'mask')
                ->data('permission', Permission::MANAGE_ROLE)
                ->active(config('laravolt.epicentrum.route.prefix') . '/roles/*');;

            $menu->add(trans('laravolt::label.permissions'), route('epicentrum::permissions.edit'))
                ->data('icon', 'shield')
                ->data('permission', Permission::MANAGE_PERMISSION)
                ->active(config('laravolt.epicentrum.route.prefix') . '/permissions/*');
        }
    }

    protected function registerBlade()
    {
        Blade::directive('role', function ($expression) {
            return "<?php if(auth()->check() && auth()->user()->hasRole($expression)): ?>";
        });

        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });
    }

    protected function registerCommand()
    {
        $this->commands(
            [
                ManageUser::class,
                ManageRole::class,
            ]
        );
    }

    protected function packagePath($path = '')
    {
        return sprintf("%s/../%s", __DIR__, $path);
    }
}
