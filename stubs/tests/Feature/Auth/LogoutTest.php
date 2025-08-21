<?php

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('it can logout', function () {
    $this->actingAs(\App\Models\User::factory()->create());

    $this->post(route('auth::logout'))
        ->assertRedirect('/');
});
