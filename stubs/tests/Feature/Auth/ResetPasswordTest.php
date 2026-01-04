<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

beforeEach(function (): void {
    $this->email = 'fulan@example.com';
    $this->table = (new User)->getTable();

    $user = User::factory()->create(['email' => $this->email]);
    $this->token = resolve(PasswordBroker::class)->createToken($user);
});

test('it can display page', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $test->get(route('auth::reset.show', $this->token))
        ->assertOk()
        ->assertSeeText(__('Email'))
        ->assertSeeText(__('Password'))
        ->assertSeeText(__('Confirm New Password'));
});

test('it can reset password', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $payload = [
        'token' => $this->token,
        'email' => $this->email,
        'password' => 'asdf1234',
        'password_confirmation' => 'asdf1234',
    ];
    $test->post(route('auth::reset.store', $this->token), $payload)
        ->assertRedirect(route('auth::login.show'));
});

test('it can handle failed password reset', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $payload = [
        'token' => $this->token,
        'email' => $this->email,
        'password' => 'asdf1234',
        'password_confirmation' => 'asdf1234',
    ];

    Password::shouldReceive('reset')->andReturn(Password::RESET_THROTTLED);

    $test->get(route('auth::reset.show', $this->token));
    $test->post(route('auth::reset.store', $this->token), $payload)
        ->assertRedirect(route('auth::reset.show', $this->token))
        ->assertSessionHasErrors('email')
        ->assertSessionHasInput('email');
});

test('it has errors if failed', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $test->post(route('auth::reset.store', 'asdf1234'))->assertSessionHasErrors();
});
