<?php

use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    config(['laravolt.platform.features.verification' => true]);
});

test('it can visit verification page', function () {
    $this->actingAs(User::factory()->create(['email_verified_at' => null]));

    $this->get(route('verification.notice'))
        ->assertSeeText(__('Verifikasi Email'))
        ->assertStatus(200);
});

test('it cannot visit verification page if already verified', function () {
    $this->actingAs(User::factory()->create(['email_verified_at' => now()]));

    $this->get(route('verification.notice'))
        ->assertRedirect(AppServiceProvider::HOME);
});

test('it can resend verification email', function () {
    $this->actingAs($user = User::factory()->create(['email_verified_at' => null]));
    Notification::fake();

    $this->post(route('verification.send'))
        ->assertSessionHas('success');

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('it cannot resend verification email if already verified', function () {
    $this->actingAs(User::factory()->create());

    $this->post(route('verification.send'))
        ->assertRedirect(AppServiceProvider::HOME);
});

test('it can verify email', function () {
    $this->actingAs($user = User::factory()->create(['email_verified_at' => null]));

    $this->mock(EmailVerificationRequest::class, function ($mock) use ($user) {
        $mock->shouldReceive('authorize')->andReturnTrue();
        $mock->shouldReceive('user')->andReturn($user);
    });

    $this->withoutMiddleware()
        ->get(route('verification.verify', ['id', 'hash']))
        ->assertRedirect(AppServiceProvider::HOME.'?verified=1');
});

test('it cannot verify email if already verified', function () {
    $this->actingAs($user = User::factory()->create());

    $this->mock(EmailVerificationRequest::class, function ($mock) use ($user) {
        $mock->shouldReceive('authorize')->andReturnTrue();
        $mock->shouldReceive('user')->andReturn($user);
    });

    $this->withoutMiddleware()
        ->get(route('verification.verify', ['id', 'hash']))
        ->assertRedirect(AppServiceProvider::HOME.'?verified=1');
});

test('validate user model concerns', function () {
    config(['app.debug' => true]);
    Auth::shouldReceive('user')->andReturn(new \stdClass);
    $this->withoutMiddleware()->post(route('verification.send'))
        ->assertSeeText(MustVerifyEmail::class)
        ->assertStatus(500);
});
