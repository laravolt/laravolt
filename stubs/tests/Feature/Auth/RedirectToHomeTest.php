<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RedirectToHomeTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        Route::get('login-success', function () {
            return 'login success';
        });
    }

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
