<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

test('it can visit my password page', function (): void {
    /** @var User|Authenticatable */
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('my::password.edit'))
        ->assertSee('password_current')
        ->assertSee('password')
        ->assertSee('password_confirmation')
        ->assertStatus(200);
});

test('it can update my password', function (): void {
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

    $user = User::query()->first();
    expect(Hash::check('new password', $user->password))->toBeTrue();
});

test('it can handle wrong current password', function (): void {
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
