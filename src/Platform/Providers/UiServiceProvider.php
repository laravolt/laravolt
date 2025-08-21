<?php

declare(strict_types=1);

namespace Laravolt\Platform\Providers;

use BladeUI\Icons\Factory;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravolt\Asset\AssetManager;
use Laravolt\Platform\Services\MenuBuilder;
use Laravolt\Platform\Services\SidebarMenu;
use Lavary\Menu\Builder;

use function Laravolt\platform_path;

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
        $this->bootConfig();

        $this->app->singleton('laravolt.menu.sidebar', fn () => new SidebarMenu);
        $this->app->singleton(
            'laravolt.menu.builder',
            function (Application $app) {
                return $app->make(MenuBuilder::class);
            }
        );

        // We add default menu in register() method,
        // to make sure it is always accessible by other providers.
        $isEnabled = config('laravolt.platform.features.enable_default_menu', true);

        if ($isEnabled) {
            /** @var \Laravolt\Platform\Services\SidebarMenu */
            $sidebarMenu = app('laravolt.menu.sidebar');
            $order = (int) config('laravolt.ui.system_menu.order');
            $sidebarMenu->registerCore(
                fn (Builder $menu) => $menu->add('Modules')->data('order', $order)
            );
            $sidebarMenu->registerCore(
                fn (Builder $menu) => $menu->add('System')->data('order', $order + 1)
            );
        }

        if ((! $this->app->runningInConsole()) || $this->app->runningUnitTests()) {
            $this->overrideUi();
            $this->registerIcons();
            $this->registerAssets();
        }
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
            ->buildMenuFromConfig()
            ->ensureAssetsExtracted();
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
     *
     * @return self
     */
    protected function bootViews()
    {
        Paginator::defaultView('laravolt::pagination.default');
        Paginator::defaultSimpleView('laravolt::pagination.simple');

        return $this;
    }

    protected function buildMenuFromConfig()
    {
        View::composer(
            'laravolt::menu.sidebar',
            function () {
                foreach (config('laravolt.menu') as $menu) {
                    $this->app['laravolt.menu.builder']->loadArray($menu);
                }

                $this->app['laravolt.menu.builder']->runCallbacks();
            }
        );

        return $this;
    }

    protected function registerAssets()
    {
        if (! $this->app->bound('laravolt.asset.group.laravolt')) {
            $this->app->singleton(
                'laravolt.asset.group.laravolt',
                function () {
                    return new AssetManager(
                        [
                            'public_dir' => public_path('laravolt'),
                            'css_dir' => '',
                            'js_dir' => '',
                        ]
                    );
                }
            );
        }
    }

    private function registerIcons()
    {
        $this->callAfterResolving(
            Factory::class,
            function (Factory $factory) {
                $icons = [
                    'fad' => platform_path('resources/icons/duotone'),
                    'far' => platform_path('resources/icons/regular'),
                    'fal' => platform_path('resources/icons/light'),
                    'fas' => platform_path('resources/icons/solid'),
                ];
                foreach ($icons as $prefix => $path) {
                    $factory->add($prefix, ['path' => $path, 'prefix' => $prefix]);
                }
            }
        );
    }

    private function overrideUi()
    {
        $this->app->booted(
            function () {
                $uiSettings = collect(config('laravolt.platform.settings'))->pluck('name')->filter()
                    ->transform(
                        function ($item) {
                            return "laravolt.ui.$item";
                        }
                    )
                    ->toArray();
                try {
                    foreach ($uiSettings as $key) {
                        $userConfig = setting($key);
                        if ($userConfig) {
                            config([$key => $userConfig]);
                        }
                    }
                } catch (QueryException $e) {

                }
            }
        );
    }

    private function ensureAssetsExtracted()
    {
        // Only run in production or when assets are missing
        if (! app()->environment('local') || $this->shouldExtractAssets()) {
            $this->extractAssets();
        }

        return $this;
    }

    private function shouldExtractAssets(): bool
    {
        // Check if icons directory exists and has files
        $iconsPath = base_path('resources/icons');
        $publicPath = public_path('laravolt');

        return ! $this->hasFiles($iconsPath) || ! $this->hasFiles($publicPath);
    }

    private function extractAssets(): void
    {
        try {
            // Extract icons.zip to resources/icons
            $this->extractFile(
                \Laravolt\platform_path('resources/icons.zip'),
                \Laravolt\platform_path('resources'),
                'icons'
            );

            // Extract assets.zip to public/laravolt
            $this->extractFile(
                \Laravolt\platform_path('resources/assets.zip'),
                \Laravolt\platform_path(''),
                'public assets'
            );
        } catch (\Exception $e) {
            // Log error but don't fail the application
            if (function_exists('logger')) {
                logger()->warning('Failed to extract Laravolt assets: '.$e->getMessage());
            }
        }
    }

    private function extractFile(string $zipPath, string $destination, string $description): bool
    {
        if (! file_exists($zipPath) || ! class_exists('\ZipArchive')) {
            return false;
        }

        $isIcons = $description === 'icons';
        $path = $isIcons ? 'icons' : 'public';
        if (is_dir($destination.DIRECTORY_SEPARATOR.$path)) {
            return false;
        }

        $zip = new \ZipArchive;
        $result = $zip->open($zipPath);

        if ($result !== true) {
            return false;
        }

        $extracted = $zip->extractTo($destination);
        $zip->close();

        return $extracted;
    }

    private function hasFiles(string $directory): bool
    {
        if (! is_dir($directory)) {
            return false;
        }

        $files = array_diff(scandir($directory), ['.', '..']);

        return count($files) > 0;
    }
}
