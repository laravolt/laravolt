<?php

declare(strict_types=1);

use App\Models\User;
use Tests\TestCase;

test('it can visit my profile page', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $test->actingAs(User::factory()->create());

    $test->get(route('my::profile.edit'))
        ->assertSee('name')
        ->assertSee('email')
        ->assertSee('timezone')
        ->assertStatus(200);
});

test('it can update my profile', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $test->actingAs(User::factory()->create());
    $payload = [
        'name' => 'fulan',
        'timezone' => 'UTC',
    ];

    $test->get(route('my::profile.edit'));
    $test->put(route('my::profile.update'), $payload)
        ->assertRedirect(route('my::profile.edit'))
        ->assertSessionHas('success');

    $test->assertDatabaseHas('users', $payload);
});

test('it can handle wrong current password', function (): void {
    /** @var TestCase $test */
    $test = $this;

    $test->actingAs(User::factory()->create());
    $payload = [
        'password_current' => 'foobar',
        'password' => 'new password',
        'password_confirmation' => 'new password',
    ];

    $test->post(route('my::password.update'), $payload)
        ->assertRedirect(route('my::password.edit'))
        ->assertSessionHas('error');
});
