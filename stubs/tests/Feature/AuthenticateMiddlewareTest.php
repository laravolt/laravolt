<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AuthenticateMiddlewareTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @test
     *
     * TODO: This test is not working
     */
    // public function test_redirect_to_login()
    // {
    //     $response = $this->get(RouteServiceProvider::HOME);
    //     $response->assertRedirect(route('auth::login.show'));
    // }

    /**
     * Middleware return nothing
     *
     * @return void
     */
    public function test_return_nothing()
    {
        $response = $this->getJson(\App\Providers\AppServiceProvider::HOME);
        $response->assertStatus(401);
    }
}
