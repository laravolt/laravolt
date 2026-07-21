<?php

declare(strict_types=1);

namespace Laravolt\Tests;

use Akaunting\Setting\Provider;
use Anhskohbo\NoCaptcha\NoCaptchaServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravolt\Platform\Models\User;
use Laravolt\Platform\Providers\EpicentrumServiceProvider;
use Laravolt\Platform\Providers\PlatformServiceProvider;
use Laravolt\Platform\Providers\UiServiceProvider;
use Lavary\Menu\ServiceProvider;
use Livewire\LivewireServiceProvider;
use URL;

trait Bootstrap
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', [
            '--path' => realpath(__DIR__.'/../database/migrations'),
            '--realpath' => true,
        ]);
    }

    /**
     * @param  Application  $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('laravolt.epicentrum.models.user', User::class);

        $app['view']->addNamespace('dummy', __DIR__.'/Dummy');
        $app['config']->set('laravolt.auth.layout', 'dummy::layout');

        URL::forceRootUrl('http://localhost');
    }

    /**
     * @param  Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        $providers = [
            ServiceProvider::class,
            EpicentrumServiceProvider::class,
            PlatformServiceProvider::class,
            UiServiceProvider::class,
            LivewireServiceProvider::class,
            Provider::class,
        ];

        if (class_exists('Anhskohbo\NoCaptcha\NoCaptchaServiceProvider')) {
            $providers[] = NoCaptchaServiceProvider::class;
        }

        return $providers;
    }

    /**
     * @param  Application  $app
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

    protected function createSessionFor(User $user): void
    {
        Cache::put("users.{$user->getKey()}.permissions", collect(['stale']), 3600);
        DB::table('sessions')->insert([
            'id' => (string) Str::ulid(),
            'user_id' => $user->getKey(),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'payload' => 'payload',
            'last_activity' => (string) Str::ulid(),
        ]);
    }

    protected function assertAccessControlInvalidatedFor(User $user): void
    {
        $this->assertFalse(Cache::has("users.{$user->getKey()}.permissions"));
        $this->assertSame(
            0,
            DB::table('sessions')->where('user_id', $user->getKey())->count(),
            'Expected no sessions to remain for user.'
        );
    }

    protected function assertAccessControlStillValidFor(User $user): void
    {
        $this->assertTrue(Cache::has("users.{$user->getKey()}.permissions"));
        $this->assertGreaterThan(
            0,
            DB::table('sessions')->where('user_id', $user->getKey())->count(),
            'Expected sessions to still exist for user.'
        );
    }
}
