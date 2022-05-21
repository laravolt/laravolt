<?php

namespace Tests\Feature\My;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MyPasswordTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_can_visit_my_password_page()
    {
        $this->actingAs(\App\Models\User::factory()->create());

        $this->get(route('my::password.edit'))
            ->assertSee('password_current')
            ->assertSee('password')
            ->assertSee('password_confirmation')
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function it_can_update_my_password()
    {
        $this->actingAs(\App\Models\User::factory()->create());
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

    /**
     * @test
     */
    public function it_can_handle_wrong_current_password()
    {
        $this->actingAs(\App\Models\User::factory()->create());
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
