<?php

declare(strict_types=1);

namespace Laravolt\Platform\Providers;

use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Console\Command;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Console\PresetCommand;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravolt\Contracts\HasRoleAndPermission;
use Laravolt\Epicentrum\Console\Commands\ManageRole;
use Laravolt\Epicentrum\Console\Commands\ManageUser;
use Laravolt\Platform\Commands\AdminCommand;
use Laravolt\Platform\Commands\LinkCommand;
use Laravolt\Platform\Commands\SyncPermission;
use Laravolt\Platform\Enums\Permission;
use Laravolt\Platform\Services\Acl;
use Laravolt\Platform\Services\LaravoltPreset;
use Laravolt\Platform\Services\Password;

class PlatformServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $commands = [
        SyncPermission::class,
        AdminCommand::class,
        LinkCommand::class,
        ManageUser::class,
        ManageRole::class,
    ];

    public function register(): void
    {
        $this->bootConfig();

        $this->commands($this->commands);

        $this->registerServices();
    }

    public function boot(Gate $gate): void
    {
        $this
            ->bootViews()
            ->bootTranslations()
            ->bootDatabase()
            ->bootRoutes()
            ->bootAcl($gate)
            ->bootMenu()
            ->bootPreset();
    }

    protected function registerServices()
    {
        // Acl
        $this->app->singleton('laravolt.acl', function ($app) {
            return new Acl();
        });

        // Password
        $this->app->singleton('laravolt.password', function ($app) {
            $app['config']['auth.password.email'] = $app['config']['laravolt.password.emails.reset'];
            $config = $this->app['config']['auth.passwords.users'];
            $token = $this->createTokenRepository($config);

            return new Password($token, $app['mailer'], $app['config']['laravolt.password.emails.new']);
        });
    }

    protected function bootConfig(): self
    {
        $this->mergeConfigFrom(platform_path('config/platform.php'), 'laravolt.platform');
        $this->mergeConfigFrom(platform_path('config/acl.php'), 'laravolt.acl');
        $this->mergeConfigFrom(platform_path('config/password.php'), 'laravolt.password');
        $this->mergeConfigFrom(platform_path('config/auth.php'), 'laravolt.auth');

        $this->publishes(
            [
                platform_path('config/platform.php') => config_path('laravolt/platform.php'),
                platform_path('config/acl.php') => config_path('laravolt/acl.php'),
                platform_path('config/password.php') => config_path('laravolt/password.php'),
                platform_path('config/auth.php') => config_path('laravolt/auth.php'),
                platform_path('config/epicentrum.php') => config_path('laravolt/epicentrum.php'),
                platform_path('config/menu.php') => config_path('laravolt/menu.php'),
            ],
            'laravolt-config'
        );

        return $this;
    }

    protected function bootDatabase(): self
    {
        $this->loadMigrationsFrom(platform_path('database/migrations'));

        return $this;
    }

    protected function bootViews(): self
    {
        $this->loadViewsFrom(platform_path('resources/views'), 'laravolt');

        $this->publishes(
            [platform_path('resources/views') => base_path('resources/views/vendor/laravolt')],
            'laravolt-views'
        );

        return $this;
    }

    protected function bootTranslations(): self
    {
        $this->loadTranslationsFrom(platform_path('resources/lang'), 'laravolt');

        return $this;
    }

    protected function bootRoutes(): self
    {
        if (config('laravolt.platform.force_https')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Route::middleware(['web', 'auth'])
            ->group(platform_path('routes/web.php'));

        return $this;
    }

    /**
     * Create a token repository instance based on the given configuration.
     *
     * @param array $config
     *
     * @return \Illuminate\Auth\Passwords\TokenRepositoryInterface
     */
    protected function createTokenRepository(array $config)
    {
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        $connection = $config['connection'] ?? null;

        return new DatabaseTokenRepository(
            $this->app['db']->connection($connection),
            $this->app['hash'],
            $config['table'],
            $key,
            $config['expire']
        );
    }

    protected function bootAcl($gate)
    {
        // register wildcard permission
        \Illuminate\Support\Facades\Gate::before(function (HasRoleAndPermission $user) {
            if ($user->hasPermission('*')) {
                return true;
            }
        });

        if ($this->hasPermissionTable()) {
            $permissions = app(config('laravolt.acl.models.permission'))->all();
            foreach ($permissions as $permission) {
                $gate->define($permission->name, function (HasRoleAndPermission $user) use ($permission) {
                    return $user->hasPermission($permission);
                });
            }
        }

        return $this;
    }

    protected function bootMenu()
    {
        if (config('laravolt.epicentrum.menu.enabled')) {
            app('laravolt.menu.sidebar')->register(function ($menu) {
                $menu = $menu->system;
                $menu->add(trans('laravolt::label.users'), route('epicentrum::users.index'))
                    ->data('icon', 'users')
                    ->data('permission', Permission::MANAGE_USER)
                    ->active(config('laravolt.epicentrum.route.prefix').'/users/*');

                $menu->add(trans('laravolt::label.roles'), route('epicentrum::roles.index'))
                    ->data('icon', 'mask')
                    ->data('permission', Permission::MANAGE_ROLE)
                    ->active(config('laravolt.epicentrum.route.prefix').'/roles/*');

                $menu->add(trans('laravolt::label.permissions'), route('epicentrum::permissions.edit'))
                    ->data('icon', 'shield')
                    ->data('permission', Permission::MANAGE_PERMISSION)
                    ->active(config('laravolt.epicentrum.route.prefix').'/permissions/*');
            });
        }

        return $this;
    }

    protected function bootPreset()
    {
        PresetCommand::macro('laravolt', function (Command $command) {
            LaravoltPreset::install();
            $command->comment('Scaffolding Laravolt skeleton');
        });

        return $this;
    }

    protected function hasPermissionTable()
    {
        try {
            $table_permissions_name = app(config('laravolt.acl.models.permission'))->getTable();

            return Schema::hasTable($table_permissions_name);
        } catch (\PDOException $e) {
            return false;
        }
    }
}
