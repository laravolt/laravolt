<?php

namespace Tests\Feature;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticateMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Middleware redirect to login
     *
     * @return void
     */
    public function test_redirect_to_login()
    {
        $response = $this->get(RouteServiceProvider::HOME);
        $response->assertRedirect(route('auth::login.show'));
    }

    /**
     * Middleware return nothing
     *
     * @return void
     */
    public function test_return_nothing()
    {
        $response = $this->getJson(RouteServiceProvider::HOME);
        $response->assertStatus(401);
    }
}
