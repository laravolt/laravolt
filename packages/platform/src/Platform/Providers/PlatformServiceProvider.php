<?php

declare(strict_types=1);

namespace Laravolt\Platform\Providers;

class PlatformServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this
            ->bootConfig()
            ->bootViews()
            ->bootTranslations()
            ->bootDatabase();
    }

    protected function bootConfig(): self
    {
        $this->mergeConfigFrom(platform_path('config/platform.php'), 'laravolt.platform');

        return $this;
    }

    protected function bootDatabase(): self
    {
        $this->loadMigrationsFrom(platform_path('database/migrations'));

        return $this;
    }

    protected function bootViews(): self
    {
        $this->loadViewsFrom(platform_path('resources/views'), 'platform');

        return $this;
    }

    protected function bootTranslations(): self
    {
        $this->loadTranslationsFrom(platform_path('resources/lang'), 'platform');

        return $this;
    }
}
