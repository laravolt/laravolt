<?php

declare(strict_types=1);

namespace Laravolt\Platform\Providers;

use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Support\Str;
use Laravolt\Platform\Services\Password;

class PlatformServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register(): void
    {
        $this->registerServices();
    }

    public function boot(): void
    {
        $this
            ->bootConfig()
            ->bootViews()
            ->bootTranslations()
            ->bootDatabase();
    }

    protected function registerServices()
    {
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
        $this->mergeConfigFrom(platform_path('config/password.php'), 'laravolt.password');

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

        return $this;
    }

    protected function bootTranslations(): self
    {
        $this->loadTranslationsFrom(platform_path('resources/lang'), 'laravolt');

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
}
