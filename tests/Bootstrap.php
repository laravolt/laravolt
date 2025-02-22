<?php

declare(strict_types=1);

namespace Laravolt\Tests;

use Anhskohbo\NoCaptcha\NoCaptchaServiceProvider;
use Laravolt\Platform\Models\User;
use Laravolt\Platform\Providers\EpicentrumServiceProvider;
use Laravolt\Platform\Providers\PlatformServiceProvider;
use Laravolt\Platform\Providers\UiServiceProvider;
use Lavary\Menu\ServiceProvider;
use Livewire\LivewireServiceProvider;

trait Bootstrap
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadLaravelMigrations();
        $this->artisan('migrate', [
            '--path' => realpath(__DIR__.'/../database/migrations'),
            '--realpath' => true,
        ]);
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('laravolt.epicentrum.models.user', User::class);

        $app['view']->addNamespace('dummy', __DIR__.'/Dummy');
        $app['config']->set('laravolt.auth.layout', 'dummy::layout');

        \URL::forceRootUrl('http://localhost');
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
            EpicentrumServiceProvider::class,
            NoCaptchaServiceProvider::class,
            PlatformServiceProvider::class,
            UiServiceProvider::class,
            LivewireServiceProvider::class,
            \Akaunting\Setting\Provider::class,
        ];
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
        ];
    }

    protected function createUser(): User
    {
        return User::create([
            'name' => 'Fulan',
            'email' => 'fulan@example.com',
            'password' => bcrypt('secret'),
        ]);
    }
}
