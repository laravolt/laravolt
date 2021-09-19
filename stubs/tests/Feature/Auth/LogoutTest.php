<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function it_can_logout()
    {
        $this->actingAs(\App\Models\User::factory()->create());

        $this->post(route('auth::logout'))
            ->assertRedirect('/');
    }
}
