<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class RedirectToHomeTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function it_can_redirected_to_home()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('auth::login.show'));

        $response->assertRedirect(RouteServiceProvider::HOME);
    }
}
