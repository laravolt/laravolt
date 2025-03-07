<?php

namespace Tests\Feature;

use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AuthenticateMiddlewareTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_redirect_to_login()
    {
        $response = $this->get(AppServiceProvider::HOME);
        $response->assertRedirect(route('auth::login.show'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_return_nothing()
    {
        $response = $this->getJson(\App\Providers\AppServiceProvider::HOME);
        $response->assertStatus(401);
    }
}
