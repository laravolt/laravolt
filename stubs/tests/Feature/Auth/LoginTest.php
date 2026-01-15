<?php

declare(strict_types=1);

use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

beforeEach(function (): void {
    Route::get('login-success', fn (): string => 'login success');
});

test('it can display login page', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $test->get(route('auth::login.show'))
        ->assertOk()
        ->assertSeeText('Email')
        ->assertSeeText('Password');
});

test('it can handle correct login', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $payload = [
        'email' => 'admin@laravolt.dev',
        'status' => 'ACTIVE',
    ];

    User::factory()->create($payload + ['password' => bcrypt('asdf1234')]);
    RateLimiter::shouldReceive('tooManyAttempts')->andReturnFalse();
    RateLimiter::shouldReceive('availableIn')->andReturn(3);
    RateLimiter::shouldReceive('clear');

    $response = $test->post(route('auth::login.store'), $payload + ['password' => 'asdf1234']);

    $response->assertRedirect(AppServiceProvider::HOME);

    $test->get(AppServiceProvider::HOME)->assertSee('Home');
});

test('it can handle wrong login', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $payload = [
        'email' => 'admin@laravolt.dev',
    ];

    User::factory()->create($payload + ['password' => bcrypt('asdf1234')]);

    $test->get(route('auth::login.show'));
    $response = $test->post(route('auth::login.store'), $payload + ['password' => 'wrong-password']);

    $response->assertRedirect(route('auth::login.show'));
});

test('ensure password required', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $test->post(route('auth::login.store'), ['email' => 'user@laravolt.dev'])
        ->assertSessionHasErrors('password');
});

test('it has errors if failed', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $test->post(route('auth::login.store'))->assertSessionHasErrors();
});

test('it has register link', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $test->app->make(Repository::class)->set('laravolt.platform.features.registration', true);

    $test->get(route('auth::login.show'))
        ->assertSeeText(trans('laravolt::auth.register_here'));
});

test('it does not have register link', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $test->app->make(Repository::class)->set('laravolt.platform.features.registration', false);

    $test->get(route('auth::login.show'))
        ->assertDontSeeText(trans('laravolt::auth.register_here'));
});

test('it has forgot password link', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $test->get(route('auth::login.show'))
        ->assertSeeText(trans('laravolt::auth.forgot_password'));
});

test('ensure rate limiter', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $limit = 5;
    $payload = [
        'email' => 'admin@laravolt.dev',
        'password' => 'etalazen',
    ];

    for ($i = 0; $i < $limit; $i++) {
        $test->post(route('auth::login.store'), $payload);
    }

    $lastRequest = $test->post(route('auth::login.store'), $payload);
    $lastRequest->assertSessionHasErrors('email');
});
