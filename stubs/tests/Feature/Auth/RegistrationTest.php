<?php

declare(strict_types=1);

use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;

test('it can display registration page', function (): void {
    $this->get(route('auth::registration.show'))
        ->assertOk()
        ->assertSeeText(__('Name'))
        ->assertSeeText(__('Email'))
        ->assertSeeText(__('Password'));
});

test('it can handle correct registration', function (): void {
    $payload = [
        'name' => 'Jon Dodo',
        'email' => 'jon@laravolt.dev',
        'password' => 'asdf1234',
        'password_confirmation' => 'asdf1234',
    ];

    $response = $this->post(route('auth::registration.store'), $payload);
    $response->assertSessionHas('success')
        ->assertRedirect(AppServiceProvider::HOME);

    $this->assertDatabaseHas('users', collect($payload)->only(['name', 'email'])->all());
});

test('it can handle correct registration with activation', function (): void {
    Notification::fake();
    $email = 'jon@laravolt.dev';

    $payload = [
        'name' => 'Jon Dodo',
        'email' => $email,
        'password' => 'asdf1234',
        'password_confirmation' => 'asdf1234',
    ];

    $response = $this->post(route('auth::registration.store'), $payload);
    $response->assertSessionHas('success')
        ->assertRedirect(AppServiceProvider::HOME);

    $this->assertDatabaseHas('users', collect($payload)->only(['name', 'email'])->all());

    Notification::assertSentTo(User::query()->first(), VerifyEmail::class);
});

test('it can auto verify email', function (): void {
    config(['laravolt.platform.features.verification' => false]);
    Notification::fake();
    $email = 'jon@laravolt.dev';

    $payload = [
        'name' => 'Jon Dodo',
        'email' => $email,
        'password' => 'asdf1234',
        'password_confirmation' => 'asdf1234',
    ];

    $this->post(route('auth::registration.store'), $payload);

    $this->assertDatabaseMissing(
        'users',
        collect($payload)->only(['name', 'email'])->all() + ['email_verified_at' => null]
    );

    Notification::assertNotSentTo(User::query()->first(), VerifyEmail::class);
});

test('it has errors if failed', function (): void {
    $this->post(route('auth::registration.store'))
        ->assertSessionHasErrors();
});
