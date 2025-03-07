<?php

namespace Tests\Feature\My;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class MyProfileTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_visit_my_profile_page()
    {
        $this->actingAs(\App\Models\User::factory()->create());

        $this->get(route('my::profile.edit'))
            ->assertSee('name')
            ->assertSee('email')
            ->assertSee('timezone')
            ->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_update_my_profile()
    {
        $this->actingAs(\App\Models\User::factory()->create());
        $payload = [
            'name' => 'fulan',
            'timezone' => 'UTC',
        ];

        $this->get(route('my::profile.edit'));
        $this->put(route('my::profile.update'), $payload)
            ->assertRedirect(route('my::profile.edit'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', $payload);
    }

    #[\PHPUnit\Framework\Attributes\Test]
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
