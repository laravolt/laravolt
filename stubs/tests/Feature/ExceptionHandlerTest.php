<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ExceptionHandlerTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_handle_authorization_exception()
    {
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
    }
}
