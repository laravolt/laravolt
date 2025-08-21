<?php

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('it can visit my profile page', function () {
    $this->actingAs(\App\Models\User::factory()->create());

    $this->get(route('my::profile.edit'))
        ->assertSee('name')
        ->assertSee('email')
        ->assertSee('timezone')
        ->assertStatus(200);
});

test('it can update my profile', function () {
    $this->actingAs(\App\Models\User::factory()->create());
    $payload = [
        'name' => 'fulan',
        'timezone' => 'UTC',
    ];

    $this->get(route('my::profile.edit'));
    $this->put(route('my::profile.update'), $payload)
        ->assertRedirect(route('my::profile.edit'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', $payload);
});

test('it can handle wrong current password', function () {
    $this->actingAs(\App\Models\User::factory()->create());
    $payload = [
        'password_current' => 'foobar',
        'password' => 'new password',
        'password_confirmation' => 'new password',
    ];

    $this->post(route('my::password.update'), $payload)
        ->assertRedirect(route('my::password.edit'))
        ->assertSessionHas('error');
});
