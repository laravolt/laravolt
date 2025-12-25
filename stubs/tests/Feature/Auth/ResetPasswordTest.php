<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Facades\Password;

beforeEach(function (): void {
    $this->email = 'fulan@example.com';
    $this->table = (new User)->getTable();

    $user = User::factory()->create(['email' => $this->email]);
    $this->token = resolve(PasswordBroker::class)->createToken($user);
});

test('it can display page', function (): void {
    $this->get(route('auth::reset.show', $this->token))
        ->assertOk()
        ->assertSeeText(__('Email'))
        ->assertSeeText(__('Password'))
        ->assertSeeText(__('Confirm New Password'));
});

test('it can reset password', function (): void {
    $payload = [
        'token' => $this->token,
        'email' => $this->email,
        'password' => 'asdf1234',
        'password_confirmation' => 'asdf1234',
    ];
    $this->post(route('auth::reset.store', $this->token), $payload)
        ->assertRedirect(route('auth::login.show'));
});

test('it can handle failed password reset', function (): void {
    $payload = [
        'token' => $this->token,
        'email' => $this->email,
        'password' => 'asdf1234',
        'password_confirmation' => 'asdf1234',
    ];

    Password::shouldReceive('reset')->andReturn(Password::RESET_THROTTLED);

    $this->get(route('auth::reset.show', $this->token));
    $this->post(route('auth::reset.store', $this->token), $payload)
        ->assertRedirect(route('auth::reset.show', $this->token))
        ->assertSessionHasErrors('email')
        ->assertSessionHasInput('email');
});

test('it has errors if failed', function (): void {
    $this->post(route('auth::reset.store', 'asdf1234'))->assertSessionHasErrors();
});
