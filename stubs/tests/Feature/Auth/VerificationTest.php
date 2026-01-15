<?php

declare(strict_types=1);

use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

beforeEach(function (): void {
    config(['laravolt.platform.features.verification' => true]);
});

test('it can visit verification page', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $test->actingAs(User::factory()->create(['email_verified_at' => null]));

    $test->get(route('verification.notice'))
        ->assertSeeText(__('Verifikasi Email'))
        ->assertStatus(200);
});

test('it cannot visit verification page if already verified', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $test->actingAs(User::factory()->create(['email_verified_at' => now()]));

    $test->get(route('verification.notice'))
        ->assertRedirect(AppServiceProvider::HOME);
});

test('it can resend verification email', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $test->actingAs($user = User::factory()->create(['email_verified_at' => null]));
    Notification::fake();

    $test->post(route('verification.send'))
        ->assertSessionHas('success');

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('it cannot resend verification email if already verified', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $test->actingAs(User::factory()->create());

    $test->post(route('verification.send'))
        ->assertRedirect(AppServiceProvider::HOME);
});

test('it can verify email', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $test->actingAs($user = User::factory()->create(['email_verified_at' => null]));

    $test->mock(EmailVerificationRequest::class, function ($mock) use ($user): void {
        $mock->shouldReceive('authorize')->andReturnTrue();
        $mock->shouldReceive('user')->andReturn($user);
    });

    $test->withoutMiddleware()
        ->get(route('verification.verify', ['id', 'hash']))
        ->assertRedirect(AppServiceProvider::HOME.'?verified=1');
});

test('it cannot verify email if already verified', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $test->actingAs($user = User::factory()->create());

    $test->mock(EmailVerificationRequest::class, function ($mock) use ($user): void {
        $mock->shouldReceive('authorize')->andReturnTrue();
        $mock->shouldReceive('user')->andReturn($user);
    });

    $test->withoutMiddleware()
        ->get(route('verification.verify', ['id', 'hash']))
        ->assertRedirect(AppServiceProvider::HOME.'?verified=1');
});

test('validate user model concerns', function (): void {
    /** @var TestCase $test */
    $test = $this;

    config(['app.debug' => true]);
    Auth::shouldReceive('user')->andReturn(new stdClass);
    $test->withoutMiddleware()->post(route('verification.send'))
        ->assertSeeText(MustVerifyEmail::class)
        ->assertStatus(500);
});
