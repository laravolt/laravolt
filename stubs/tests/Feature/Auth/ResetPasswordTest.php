<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

/**
 * @return array{email: string, token: string}
 */
function createUserWithResetToken(): array
{
    $email = 'fulan@example.com';
    $user = User::factory()->create(['email' => $email]);
    $token = resolve(PasswordBroker::class)->createToken($user);

    return ['email' => $email, 'token' => $token];
}

test('it can display page', function (): void {
    /** @var TestCase $test */
    $test = $this;

    ['token' => $token] = createUserWithResetToken();

    $test->get(route('auth::reset.show', $token))
        ->assertOk()
        ->assertSeeText(__('Email'))
        ->assertSeeText(__('Password'))
        ->assertSeeText(__('Confirm New Password'));
});

test('it can reset password', function (): void {
    /** @var TestCase $test */
    $test = $this;

    ['email' => $email, 'token' => $token] = createUserWithResetToken();

    $payload = [
        'token' => $token,
        'email' => $email,
        'password' => 'asdf1234',
        'password_confirmation' => 'asdf1234',
    ];
    $test->post(route('auth::reset.store', $token), $payload)
        ->assertRedirect(route('auth::login.show'));
});

test('it can handle failed password reset', function (): void {
    /** @var TestCase $test */
    $test = $this;

    ['email' => $email, 'token' => $token] = createUserWithResetToken();

    $payload = [
        'token' => $token,
        'email' => $email,
        'password' => 'asdf1234',
        'password_confirmation' => 'asdf1234',
    ];

    Password::shouldReceive('reset')->andReturn(Password::RESET_THROTTLED);

    $test->get(route('auth::reset.show', $token));
    $test->post(route('auth::reset.store', $token), $payload)
        ->assertRedirect(route('auth::reset.show', $token))
        ->assertSessionHasErrors('email')
        ->assertSessionHasInput('email');
});

test('it has errors if failed', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $test->post(route('auth::reset.store', 'asdf1234'))->assertSessionHasErrors();
});
