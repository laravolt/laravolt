<?php

use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(InteractsWithDatabase::class, LazilyRefreshDatabase::class);

test('it can display registration page', function () {
    $this->get(route('auth::registration.show'))
        ->assertOk()
        ->assertSeeText(__('Name'))
        ->assertSeeText(__('Email'))
        ->assertSeeText(__('Password'));
});

test('it can handle correct registration', function () {
    $payload = [
        'name' => 'Jon Dodo',
        'email' => 'jon@laravolt.dev',
        'password' => 'asdf1234',
        'password_confirmation' => 'asdf1234',
    ];

    $response = $this->post(route('auth::registration.store'), $payload);
    $response->assertSessionHas('success')
        ->assertRedirect(AppServiceProvider::HOME);

    $this->assertDatabaseHas('users', collect($payload)->only(['name', 'email'])->toArray());
});

test('it can handle correct registration with activation', function () {
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

    $this->assertDatabaseHas('users', collect($payload)->only(['name', 'email'])->toArray());

    Notification::assertSentTo(User::first(), VerifyEmail::class);
});

test('it can auto verify email', function () {
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
        collect($payload)->only(['name', 'email'])->toArray() + ['email_verified_at' => null]
    );

    Notification::assertNotSentTo(User::first(), VerifyEmail::class);
});

test('it has errors if failed', function () {
    $this->post(route('auth::registration.store'))
        ->assertSessionHasErrors();
});
