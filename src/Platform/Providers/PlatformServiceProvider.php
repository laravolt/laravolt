<?php

declare(strict_types=1);

namespace Laravolt\Platform\Providers;

use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravolt\Contracts\HasRoleAndPermission;
use Laravolt\Epicentrum\Console\Commands\ManageRole;
use Laravolt\Epicentrum\Console\Commands\ManageUser;
use Laravolt\Platform\Commands\AdminCommand;
use Laravolt\Platform\Commands\InstallCommand;
use Laravolt\Platform\Commands\LinkCommand;
use Laravolt\Platform\Commands\MakeChartCommnad;
use Laravolt\Platform\Commands\MakeTableCommnad;
use Laravolt\Platform\Commands\SyncPermission;
use Laravolt\Platform\Services\Acl;
use Laravolt\Platform\Services\Password;

class PlatformServiceProvider extends ServiceProvider
{
    protected $commands = [
        AdminCommand::class,
        InstallCommand::class,
        MakeTableCommnad::class,
        MakeChartCommnad::class,
        ManageRole::class,
        ManageUser::class,
        LinkCommand::class,
        SyncPermission::class,
    ];

    public function register(): void
    {
        $this->registerConfig();

        $this->commands($this->commands);

        $this->registerServices();

        $this->publishSkeleton();
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
            ->bootComponents();
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

        if (config('laravolt.platform.features.captcha')) {
            $this->app->register('Anhskohbo\NoCaptcha\NoCaptchaServiceProvider');
        }
    }

    protected function registerConfig(): self
    {
        $config = ['epicentrum', 'password', 'platform', 'ui'];
        $publishes = [];
        foreach ($config as $c) {
            $this->mergeConfigFrom(platform_path("config/$c.php"), "laravolt.$c");
            $publishes[platform_path("config/$c.php")] = config_path("laravolt/$c.php");
        }

        $this->publishes($publishes, ['laravolt-config', 'config']);

        return $this;
    }

    protected function bootDatabase(): self
    {
        $migrationFolder = 'database/migrations';

        $this->publishes(
            [platform_path($migrationFolder) => base_path($migrationFolder)],
            'laravolt-migrations'
        );

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
        if (Str::startsWith(config('app.url'), 'https')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        include platform_path('routes/web.php');

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
        \Illuminate\Support\Facades\Gate::before(function (HasRoleAndPermission $user, $ability, $models) {
            if ($user->hasPermission('*') && empty($models)) {
                return true;
            }
        });

        if ($this->hasPermissionTable()) {
            $permissions = app(config('laravolt.epicentrum.models.permission'))->all();
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
        $menu = platform_path('config/menu/system.php');
        $this->mergeConfigFrom($menu, 'laravolt.menu.system');
        $this->publishes([$menu => config_path('laravolt/menu/system.php')], ['laravolt-config', 'config']);

        return $this;
    }

    protected function bootComponents()
    {
        \Blade::componentNamespace('Laravolt\\Platform\\Components', 'laravolt');
    }

    protected function hasPermissionTable()
    {
        try {
            $table_permissions_name = app(config('laravolt.epicentrum.models.permission'))->getTable();

            return Schema::hasTable($table_permissions_name);
        } catch (\PDOException $e) {
            return false;
        }
    }

    private function publishSkeleton()
    {
        $this->publishes(
            [platform_path('stubs') => base_path()],
            ['laravolt-skeleton']
        );
    }
}
