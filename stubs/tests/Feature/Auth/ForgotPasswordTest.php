<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Contracts\Config\Repository;
use Laravolt\Platform\Services\Password;
use Mockery\MockInterface;
use Tests\TestCase;

test('it can get forgot password page', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $test->get(route('auth::forgot.store'))
        ->assertSee('email')
        ->assertStatus(200);
});

test('it can handle correct email', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $payload = [
        'email' => 'admin@laravolt.dev',
    ];

    User::factory()->create($payload);

    $test->post(route('auth::forgot.store'), $payload)
        ->assertRedirect(route('auth::forgot.show'))
        ->assertSessionHas('success');
});

test('it can handle wrong email', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $payload = [
        'email' => 'zombie@laravolt.dev',
    ];

    // We must visit form at first, to mimic browser history a.k.a redirect back
    $test->get(route('auth::forgot.show'));

    $test->post(route('auth::forgot.store'), $payload)
        ->assertRedirect(route('auth::forgot.show'))
        ->assertSessionHasErrors('email');
});

test('it has errors if failed', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $test->post(route('auth::forgot.store'))->assertSessionHasErrors();
});

test('it can handle send email failure', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $payload = [
        'email' => 'admin@laravolt.dev',
    ];

    $test->instance('laravolt.password', Mockery::mock(Password::class, function (MockInterface $mock): void {
        $mock->shouldReceive('sendResetLink')->once()->andReturn(Illuminate\Support\Facades\Password::RESET_THROTTLED);
    }));

    User::factory()->create($payload);

    $test->post(route('auth::forgot.store'), $payload)
        ->assertRedirect(route('auth::forgot.show'))
        ->assertSessionHas('error');
});

test('it has register link', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $test->get(route('auth::forgot.show'))->assertSeeText(trans('laravolt::auth.register_here'));
});

test('it does not have register link if registration disabled', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $test->app->make(Repository::class)->set('laravolt.platform.features.registration', false);
    $test->get(route('auth::forgot.show'))->assertDontSeeText(trans('laravolt::auth.register_here'));
});
