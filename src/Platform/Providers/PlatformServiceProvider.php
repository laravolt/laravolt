<?php

declare(strict_types=1);

namespace Laravolt\Platform\Providers;

use App\Enums\Permission;
use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravolt\Contracts\HasRoleAndPermission;
use Laravolt\Epicentrum\Auth\CacheEloquentUserProvider;
use Laravolt\Epicentrum\Auth\UserObserver;
use Laravolt\Epicentrum\Console\Commands\ManageRole;
use Laravolt\Epicentrum\Console\Commands\ManageUser;
use Laravolt\Platform\Commands\AdminCommand;
use Laravolt\Platform\Commands\ExtractAssetsCommand;
use Laravolt\Platform\Commands\InstallCommand;
use Laravolt\Platform\Commands\LinkCommand;
use Laravolt\Platform\Commands\MakeChartCommnad;
use Laravolt\Platform\Commands\MakeStatisticCommnad;
use Laravolt\Platform\Commands\MakeTableCommnad;
use Laravolt\Platform\Commands\MakeViewCommnad;
use Laravolt\Platform\Commands\Pest4InstallCommand;
use Laravolt\Platform\Commands\SyncPermission;
use Laravolt\Platform\Components\Alert;
use Laravolt\Platform\Components\Avatar;
use Laravolt\Platform\Components\Backlink;
use Laravolt\Platform\Components\Badge;
use Laravolt\Platform\Components\BrandImage;
use Laravolt\Platform\Components\Breadcrumb;
use Laravolt\Platform\Components\Button;
use Laravolt\Platform\Components\Card;
use Laravolt\Platform\Components\CardFooter;
use Laravolt\Platform\Components\Cards;
use Laravolt\Platform\Components\Checkbox;
use Laravolt\Platform\Components\Dropdown;
use Laravolt\Platform\Components\FileInput;
use Laravolt\Platform\Components\Form;
use Laravolt\Platform\Components\Icon;
use Laravolt\Platform\Components\Item;
use Laravolt\Platform\Components\Label;
use Laravolt\Platform\Components\Link;
use Laravolt\Platform\Components\LinkButton;
use Laravolt\Platform\Components\ListGroup;
use Laravolt\Platform\Components\MediaLibrary;
use Laravolt\Platform\Components\Modal;
use Laravolt\Platform\Components\Offcanvas;
use Laravolt\Platform\Components\Pagination;
use Laravolt\Platform\Components\Panel;
use Laravolt\Platform\Components\Popover;
use Laravolt\Platform\Components\Progress;
use Laravolt\Platform\Components\Radio;
use Laravolt\Platform\Components\Sidebar;
use Laravolt\Platform\Components\Skeleton;
use Laravolt\Platform\Components\Stepper;
use Laravolt\Platform\Components\Tab;
use Laravolt\Platform\Components\TabContent;
use Laravolt\Platform\Components\ToggleSwitch;
use Laravolt\Platform\Components\Table;
use Laravolt\Platform\Components\Titlebar;
use Laravolt\Platform\Components\Toast;
use Laravolt\Platform\Components\Tooltip;
use Laravolt\Platform\Services\Acl;
use Laravolt\Platform\Services\LaravoltBladeDirectives;
use Laravolt\Platform\Services\Password;
use Laravolt\Ui\ModalBag;
use Livewire\Livewire;

use function Laravolt\platform_path;

class PlatformServiceProvider extends ServiceProvider
{
    protected $commands = [
        AdminCommand::class,
        ExtractAssetsCommand::class,
        InstallCommand::class,
        LinkCommand::class,
        MakeTableCommnad::class,
        MakeChartCommnad::class,
        MakeViewCommnad::class,
        MakeStatisticCommnad::class,
        ManageRole::class,
        ManageUser::class,
        Pest4InstallCommand::class,
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
            ->bootCustomAuthProvider()
            ->bootBladeDirectives()
            ->adjustVendorConfig();
    }

    protected function registerServices()
    {
        // Acl
        $this->app->singleton(
            'laravolt.acl',
            function ($app) {
                return new Acl;
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
        // Define migrations to exclude
        $excludedMigrations = [
            '0001_01_01_000000_create_users_table.php',
            '0001_01_01_000001_create_cache_table.php',
            '0001_01_01_000002_create_jobs_table.php',
        ];
        // Get all migration files from the platform path
        $allMigrations = glob(database_path('migrations').'/*.php');
        // Filter out the excluded migrations
        // and remove them from the database/migrations folder
        // This is to prevent migration conflicts
        // when using laravolt/laravolt package
        foreach ($allMigrations as $migration) {
            $filename = basename($migration);
            if (in_array($filename, $excludedMigrations)) {
                // remove existing migration files
                try {
                    \Illuminate\Support\Facades\File::delete($migration);
                } catch (\Exception $e) {
                    // Log the error or handle it as needed
                    \Log::error("Failed to delete migration file: {$migration}. Error: {$e->getMessage()}");
                }
            }
        }

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
            function ($user, $ability, $models) {
                if (empty($models) && $user instanceof HasRoleAndPermission && $user->hasPermission('*')) {
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
        $isEnabled = config('laravolt.platform.features.enable_default_menu', true);

        if ($isEnabled) {
            $keys = ['system'];
            $publishes = [];
            foreach ($keys as $key) {
                $menu = platform_path("config/menu/$key.php");
                $this->mergeConfigFrom($menu, "laravolt.menu.$key");
                $publishes[$menu] = config_path("laravolt/menu/$key.php");
            }
            $this->publishes($publishes, ['laravolt-config', 'config']);
        }

        return $this;
    }

    protected function bootComponents()
    {
        $components = [
            // Layout Components
            'base' => 'laravolt::layout.base',
            'auth' => 'laravolt::layout.auth',
            'app' => 'laravolt::layout.app',
            'public' => 'laravolt::layout.public',

            // Base Components
            'inspire' => 'laravolt::components.inspire',
            'grid' => 'laravolt::components.grid',
            'row' => 'laravolt::components.row',
            'col' => 'laravolt::components.col',
            'modal' => 'laravolt::components.modal',
            'workflow-diagram-button' => 'laravolt::workflow.components.diagram-button',

            // New Preline UI Components
            'alert' => 'laravolt::components.alert',
            'avatar' => 'laravolt::components.avatar',
            'badge' => 'laravolt::components.badge',
            'breadcrumb' => 'laravolt::components.breadcrumb',
            'button' => 'laravolt::components.button',
            'card' => 'laravolt::components.card',
            'card-footer' => 'laravolt::components.card-footer',
            'checkbox' => 'laravolt::components.checkbox',
            'dropdown' => 'laravolt::components.dropdown',
            'file-input' => 'laravolt::components.file-input',
            'flash' => 'laravolt::components.flash',
            'input' => 'laravolt::components.input',
            'link' => 'laravolt::components.link',
            'link-button' => 'laravolt::components.link-button',
            'list-group' => 'laravolt::components.list-group',
            'offcanvas' => 'laravolt::components.offcanvas',
            'pagination' => 'laravolt::components.pagination',
            'panel' => 'laravolt::components.panel',
            'popover' => 'laravolt::components.popover',
            'progress' => 'laravolt::components.progress',
            'radio' => 'laravolt::components.radio',
            'select' => 'laravolt::components.select',
            'sidebar' => 'laravolt::components.sidebar',
            'skeleton' => 'laravolt::components.skeleton',
            'stepper' => 'laravolt::components.stepper',
            'switch' => 'laravolt::components.switch',
            'tab' => 'laravolt::components.tab',
            'tab-content' => 'laravolt::components.tab-content',
            'table' => 'laravolt::components.table',
            'textarea' => 'laravolt::components.textarea',
            'toast' => 'laravolt::components.toast',
            'tooltip' => 'laravolt::components.tooltip',

            // Class-based Components
            Alert::class,
            Avatar::class,
            Backlink::class,
            Badge::class,
            BrandImage::class,
            Breadcrumb::class,
            Button::class,
            Card::class,
            CardFooter::class,
            Cards::class,
            Checkbox::class,
            Dropdown::class,
            FileInput::class,
            Form::class,
            Icon::class,
            Item::class,
            Label::class,
            Link::class,
            LinkButton::class,
            ListGroup::class,
            MediaLibrary::class,
            Offcanvas::class,
            Pagination::class,
            Panel::class,
            Popover::class,
            Progress::class,
            Radio::class,
            Sidebar::class,
            Skeleton::class,
            Stepper::class,
            Tab::class,
            TabContent::class,
            Table::class,
            ToggleSwitch::class,
            Titlebar::class,
            Toast::class,
            Tooltip::class,
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

    protected function bootCustomAuthProvider()
    {
        Auth::provider('eloquent-cached', function () {
            return new CacheEloquentUserProvider($this->app['hash'], $this->app['config']['auth.providers.users.model']);
        });

        if ($this->app['config']['auth.providers.users.driver'] === 'eloquent-cached') {
            call_user_func(config('laravolt.epicentrum.models.user').'::observe', UserObserver::class);
        }

        return $this;
    }

    protected function bootBladeDirectives()
    {
        Blade::directive('laravoltScripts', [LaravoltBladeDirectives::class, 'scripts']);
        Blade::directive('laravoltStyles', [LaravoltBladeDirectives::class, 'styles']);

        // BasicTable directives
        Blade::directive('basictable', [LaravoltBladeDirectives::class, 'basictable']);
        Blade::directive('basictableResponsive', [LaravoltBladeDirectives::class, 'basictableResponsive']);
        Blade::directive('basictableCompact', [LaravoltBladeDirectives::class, 'basictableCompact']);
        Blade::directive('basictableInline', [LaravoltBladeDirectives::class, 'basictableInline']);
        Blade::directive('basictableScrollable', [LaravoltBladeDirectives::class, 'basictableScrollable']);

        return $this;
    }
}
