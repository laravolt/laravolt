<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('it can redirected to home', function () {
    /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('auth::login.show'));

    $response->assertRedirect(\App\Providers\AppServiceProvider::HOME);
});
