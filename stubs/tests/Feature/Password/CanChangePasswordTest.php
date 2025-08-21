<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(LazilyRefreshDatabase::class);

test('password changed at filled', function () {
    $user = User::factory()->create();

    expect($user->password_changed_at)->not->toBeNull();
});

test('can change password', function () {
    $user = User::factory()->create();

    $user->setPassword('secret2', true);
    expect($user->password_changed_at)->toBeNull();

    $user->setPassword('secret2', false);
    expect($user->password_changed_at)->not->toBeNull();

    expect(Hash::check('secret2', $user->password))->toBeTrue();
});

test('password must be changed', function () {
    $user = User::factory()->create();

    $user->setPassword('secret2', true);
    expect($user->passwordMustBeChanged(1))->toBeTrue();

    $user->setPassword('secret2', false);
    expect($user->passwordMustBeChanged(1))->toBeFalse();

    $user->setPassword('secret2', false);
    expect($user->passwordMustBeChanged(null))->toBeFalse();

    $user->setPassword('secret2', true);
    expect($user->passwordMustBeChanged(null))->toBeTrue();
});

test('password must be changed duration', function () {
    $user = User::factory()->create();

    // Lets assume user changed their password 2 days ago,
    $user->password_changed_at = Carbon::now()->subDays(2);

    // So, when we have password duration = 2 days, user must change their password
    // because it is already equal with the limit
    expect($user->passwordMustBeChanged(2))->toBeTrue();

    // But, if we have password duration = 3 days, user still allowed to use their passwrod
    // because it has 1 day remaining.
    expect($user->passwordMustBeChanged(3))->toBeFalse();
});
