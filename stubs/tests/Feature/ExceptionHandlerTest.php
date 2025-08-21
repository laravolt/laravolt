<?php

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(LazilyRefreshDatabase::class);

test('it can handle authorization exception', function () {
    Route::get('admin/page', static fn () => 'hello')->middleware('can:access-admin');
    Route::get('livewire/foo', static fn () => 'hello')->middleware('can:access-admin');

    // web visit
    $this->get('admin/page')->assertStatus(403);

    // JSON (API) visit
    $this->json('GET', 'admin/page')
        ->assertStatus(403)
        ->assertJson(['message' => 'This action is unauthorized.']);

    // Livewire visit
    $this->get('livewire/foo')
        ->assertStatus(403);
});
