<?php

namespace Tests\Feature\My;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MyPasswordTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_visit_my_password_page()
    {
        /** @var User|Authenticatable */
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('my::password.edit'))
            ->assertSee('password_current')
            ->assertSee('password')
            ->assertSee('password_confirmation')
            ->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_update_my_password()
    {
        /** @var User|Authenticatable */
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'password_current' => 'password',
            'password' => 'new password',
            'password_confirmation' => 'new password',
        ];

        $this->post(route('my::password.update'), $payload)
            ->assertRedirect(route('my::password.edit'))
            ->assertSessionHas('success');

        $user = User::first();
        $this->assertTrue(Hash::check('new password', $user->password));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_handle_wrong_current_password()
    {
        /** @var User|Authenticatable */
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'password_current' => 'foobar',
            'password' => 'new password',
            'password_confirmation' => 'new password',
        ];

        $this->post(route('my::password.update'), $payload)
            ->assertRedirect(route('my::password.edit'))
            ->assertSessionHas('error');
    }
}
