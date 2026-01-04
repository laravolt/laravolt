<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

test('it can handle authorization exception', function (): void {
    /** @var TestCase $test */
    $test = $this;

    Route::get('admin/page', static fn (): string => 'hello')->middleware('can:access-admin');
    Route::get('livewire/foo', static fn (): string => 'hello')->middleware('can:access-admin');

    // web visit
    $test->get('admin/page')->assertStatus(403);

    // JSON (API) visit
    $test->json('GET', 'admin/page')
        ->assertStatus(403)
        ->assertJson(['message' => 'This action is unauthorized.']);

    // Livewire visit
    $test->get('livewire/foo')
        ->assertStatus(403);
});
