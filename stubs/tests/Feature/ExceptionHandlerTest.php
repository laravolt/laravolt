<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

test('it can handle authorization exception', function (): void {
    Route::get('admin/page', static fn (): string => 'hello')->middleware('can:access-admin');
    Route::get('livewire/foo', static fn (): string => 'hello')->middleware('can:access-admin');

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
