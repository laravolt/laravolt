<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravolt\Platform\Services\Password;
use Mockery\MockInterface;

uses(LazilyRefreshDatabase::class);

test('it can get forgot password page', function () {
    $this->get(route('auth::forgot.store'))
        ->assertSee('email')
        ->assertStatus(200);
});

test('it can handle correct email', function () {
    $payload = [
        'email' => 'admin@laravolt.dev',
    ];

    User::factory()->create($payload);

    $this->post(route('auth::forgot.store'), $payload)
        ->assertRedirect(route('auth::forgot.show'))
        ->assertSessionHas('success');
});

test('it can handle wrong email', function () {
    $payload = [
        'email' => 'zombie@laravolt.dev',
    ];

    // We must visit form at first, to mimic browser history a.k.a redirect back
    $this->get(route('auth::forgot.show'));

    $this->post(route('auth::forgot.store'), $payload)
        ->assertRedirect(route('auth::forgot.show'))
        ->assertSessionHasErrors('email');
});

test('it has errors if failed', function () {
    $this->post(route('auth::forgot.store'))->assertSessionHasErrors();
});

test('it can handle send email failure', function () {
    $payload = [
        'email' => 'admin@laravolt.dev',
    ];

    $this->instance('laravolt.password', \Mockery::mock(Password::class, function (MockInterface $mock) {
        $mock->shouldReceive('sendResetLink')->once()->andReturn(\Password::RESET_THROTTLED);
    }));

    User::factory()->create($payload);

    $this->post(route('auth::forgot.store'), $payload)
        ->assertRedirect(route('auth::forgot.show'))
        ->assertSessionHas('error');
});

test('it has register link', function () {
    $this->get(route('auth::forgot.show'))->assertSeeText(trans('laravolt::auth.register_here'));
});

test('it does not have register link if registration disabled', function () {
    $this->app['config']->set('laravolt.platform.features.registration', false);
    $this->get(route('auth::forgot.show'))->assertDontSeeText(trans('laravolt::auth.register_here'));
});
