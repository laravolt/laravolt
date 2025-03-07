<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class RedirectToHomeTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_redirected_to_home()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('auth::login.show'));

        $response->assertRedirect(\App\Providers\AppServiceProvider::HOME);
    }
}
