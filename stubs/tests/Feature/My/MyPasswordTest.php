<?php

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(LazilyRefreshDatabase::class);

test('it can visit my password page', function () {
    /** @var User|Authenticatable */
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('my::password.edit'))
        ->assertSee('password_current')
        ->assertSee('password')
        ->assertSee('password_confirmation')
        ->assertStatus(200);
});

test('it can update my password', function () {
    /** @var User|Authenticatable */
    $user = User::factory()->create();
    $this->actingAs($user);

    $payload = [
        'password_current' => 'password',
        'password' => 'new password',
        'password_confirmation' => 'new password',
    ];

    $this->post(route('my::password.update'), $payload)
        ->assertRedirect(route('my::password.edit'))
        ->assertSessionHas('success');

    $user = User::first();
    expect(Hash::check('new password', $user->password))->toBeTrue();
});

test('it can handle wrong current password', function () {
    /** @var User|Authenticatable */
    $user = User::factory()->create();
    $this->actingAs($user);

    $payload = [
        'password_current' => 'foobar',
        'password' => 'new password',
        'password_confirmation' => 'new password',
    ];

    $this->post(route('my::password.update'), $payload)
        ->assertRedirect(route('my::password.edit'))
        ->assertSessionHas('error');
});
