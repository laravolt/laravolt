<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_logout()
    {
        $this->actingAs(\App\Models\User::factory()->create());

        $this->post(route('auth::logout'))
            ->assertRedirect('/');
    }
}
