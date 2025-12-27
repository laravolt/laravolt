<?php

declare(strict_types=1);

use App\Models\User;
use Laravolt\Platform\Services\Password;
use Mockery\MockInterface;

test('it can get forgot password page', function (): void {
    $this->get(route('auth::forgot.store'))
        ->assertSee('email')
        ->assertStatus(200);
});

test('it can handle correct email', function (): void {
    $payload = [
        'email' => 'admin@laravolt.dev',
    ];

    User::factory()->create($payload);

    $this->post(route('auth::forgot.store'), $payload)
        ->assertRedirect(route('auth::forgot.show'))
        ->assertSessionHas('success');
});

test('it can handle wrong email', function (): void {
    $payload = [
        'email' => 'zombie@laravolt.dev',
    ];

    // We must visit form at first, to mimic browser history a.k.a redirect back
    $this->get(route('auth::forgot.show'));

    $this->post(route('auth::forgot.store'), $payload)
        ->assertRedirect(route('auth::forgot.show'))
        ->assertSessionHasErrors('email');
});

test('it has errors if failed', function (): void {
    $this->post(route('auth::forgot.store'))->assertSessionHasErrors();
});

test('it can handle send email failure', function (): void {
    $payload = [
        'email' => 'admin@laravolt.dev',
    ];

    $this->instance('laravolt.password', Mockery::mock(Password::class, function (MockInterface $mock): void {
        $mock->shouldReceive('sendResetLink')->once()->andReturn(Illuminate\Support\Facades\Password::RESET_THROTTLED);
    }));

    User::factory()->create($payload);

    $this->post(route('auth::forgot.store'), $payload)
        ->assertRedirect(route('auth::forgot.show'))
        ->assertSessionHas('error');
});

test('it has register link', function (): void {
    $this->get(route('auth::forgot.show'))->assertSeeText(trans('laravolt::auth.register_here'));
});

test('it does not have register link if registration disabled', function (): void {
    $this->app['config']->set('laravolt.platform.features.registration', false);
    $this->get(route('auth::forgot.show'))->assertDontSeeText(trans('laravolt::auth.register_here'));
});
