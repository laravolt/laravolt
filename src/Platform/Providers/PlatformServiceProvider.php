<?php

declare(strict_types=1);

namespace Laravolt\Platform\Providers;

use App\Enums\Permission;
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
use Laravolt\Platform\Commands\MakeChartCommnad;
use Laravolt\Platform\Commands\MakeStatisticCommnad;
use Laravolt\Platform\Commands\MakeTableCommnad;
use Laravolt\Platform\Commands\MakeViewCommnad;
use Laravolt\Platform\Commands\SyncPermission;
use Laravolt\Platform\Components\Backlink;
use Laravolt\Platform\Components\BrandImage;
use Laravolt\Platform\Components\Breadcrumb;
use Laravolt\Platform\Components\Button;
use Laravolt\Platform\Components\Card;
use Laravolt\Platform\Components\CardFooter;
use Laravolt\Platform\Components\Cards;
use Laravolt\Platform\Components\Form;
use Laravolt\Platform\Components\Icon;
use Laravolt\Platform\Components\Item;
use Laravolt\Platform\Components\Label;
use Laravolt\Platform\Components\Link;
use Laravolt\Platform\Components\LinkButton;
use Laravolt\Platform\Components\MediaLibrary;
use Laravolt\Platform\Components\Panel;
use Laravolt\Platform\Components\Tab;
use Laravolt\Platform\Components\TabContent;
use Laravolt\Platform\Components\Titlebar;
use Laravolt\Platform\Services\Acl;
use Laravolt\Platform\Services\Password;
use Laravolt\Ui\ModalBag;
use Livewire\Livewire;
use function Laravolt\platform_path;

class PlatformServiceProvider extends ServiceProvider
{
    protected $commands = [
        AdminCommand::class,
        InstallCommand::class,
        MakeTableCommnad::class,
        MakeChartCommnad::class,
        MakeViewCommnad::class,
        MakeStatisticCommnad::class,
        ManageRole::class,
        ManageUser::class,
        SyncPermission::class,
    ];

    public function register(): void
    {
        $this->registerConfig();

        $this->commands($this->commands);

        $this->registerServices();

        $this->publishSkeleton();

        $this->publishAssets();
    }

    public function boot(Gate $gate): void
    {
        $this
            ->bootViews()
            ->bootTranslations()
            ->bootDatabase()
            ->bootAsset()
            ->bootRoutes()
            ->bootAcl($gate)
            ->bootMenu()
            ->bootComponents()
            ->adjustVendorConfig();
    }

    protected function registerServices()
    {
        // Acl
        $this->app->singleton(
            'laravolt.acl',
            function ($app) {
                return new Acl();
            }
        );

        // Password
        $this->app->singleton(
            'laravolt.password',
            function ($app) {
                $app['config']['auth.password.email'] = $app['config']['laravolt.password.emails.reset'];
                $config = $this->app['config']['auth.passwords.users'];
                $token = $this->createTokenRepository($config);

                return new Password($token, $app['mailer'], $app['config']['laravolt.password.emails.new']);
            }
        );

        if (config('laravolt.platform.features.captcha')) {
            $this->app->register('Anhskohbo\NoCaptcha\NoCaptchaServiceProvider');
        }
    }

    protected function registerConfig(): self
    {
        $configFiles = \File::files(platform_path('config'));
        $publishes = [];
        foreach ($configFiles as $file) {
            $c = $file->getBasename('.php');
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

    protected function bootAsset(): self
    {
        $this->publishes(
            [platform_path('public') => base_path('public/laravolt')],
            'laravolt-asset'
        );

        return $this;
    }

    protected function bootViews(): self
    {
        $this->loadViewsFrom(
            [
                platform_path('resources/views'),
                platform_path('packages/workflow/resources/views'),
            ],
            'laravolt'
        );

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

        if (! $this->app->routesAreCached()) {
            include platform_path('routes/web.php');
        }

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
        \Illuminate\Support\Facades\Gate::before(
            function (HasRoleAndPermission $user, $ability, $models) {
                if ($user->hasPermission('*') && empty($models)) {
                    return true;
                }
            }
        );

        if ($this->hasPermissionTable()) {
            $permissions = app(config('laravolt.epicentrum.models.permission'))->all();
            foreach ($permissions as $permission) {
                $gate->define(
                    $permission->name,
                    function (HasRoleAndPermission $user) use ($permission) {
                        return $user->hasPermission($permission);
                    }
                );
            }
        }

        // Auto register Permission in App\Enums\Permission class
        if (class_exists(Permission::class)) {
            app('laravolt.acl')->registerPermission(Permission::asArray());
        }

        return $this;
    }

    protected function bootMenu()
    {
        $keys = ['system'];
        $publishes = [];
        foreach ($keys as $key) {
            $menu = platform_path("config/menu/$key.php");
            $this->mergeConfigFrom($menu, "laravolt.menu.$key");
            $publishes[$menu] = config_path("laravolt/menu/$key.php");
        }
        $this->publishes($publishes, ['laravolt-config', 'config']);

        return $this;
    }

    protected function bootComponents()
    {
        $components = [
            'base' => 'laravolt::layout.base',
            'auth' => 'laravolt::layout.auth',
            'app' => 'laravolt::layout.app',
            'public' => 'laravolt::layout.public',
            'inspire' => 'laravolt::components.inspire',
            'grid' => 'laravolt::components.grid',
            'row' => 'laravolt::components.row',
            'col' => 'laravolt::components.col',
            'modal' => 'laravolt::components.modal',
            'workflow-diagram-button' => 'laravolt::workflow.components.diagram-button',
            Backlink::class,
            BrandImage::class,
            Breadcrumb::class,
            Button::class,
            Card::class,
            CardFooter::class,
            Cards::class,
            Form::class,
            Icon::class,
            Item::class,
            Label::class,
            Link::class,
            LinkButton::class,
            MediaLibrary::class,
            Panel::class,
            Tab::class,
            TabContent::class,
            Titlebar::class,
        ];

        $this->loadViewComponentsAs('volt', $components);

        Livewire::component('volt-modal-bag', ModalBag::class);

        return $this;
    }

    protected function adjustVendorConfig()
    {
        config(['laravel-menu.settings.default.cascade_data' => false]);

        return $this;
    }

    protected function hasPermissionTable()
    {
        try {
            return Schema::hasTable(app(config('laravolt.epicentrum.models.permission'))->getTable());
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

    private function publishAssets()
    {
        $this->publishes(
            [platform_path('public') => public_path('laravolt')],
            ['laravolt-assets']
        );
    }
}
