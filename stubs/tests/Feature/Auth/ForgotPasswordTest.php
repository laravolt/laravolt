<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravolt\Platform\Services\Password;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[Test]
    public function it_can_get_forgot_password_page()
    {
        $this->get(route('auth::forgot.store'))
            ->assertSee('email')
            ->assertStatus(200);
    }

    #[Test]
    public function it_can_handle_correct_email()
    {
        $payload = [
            'email' => 'admin@laravolt.dev',
        ];

        User::factory()->create($payload);

        $this->post(route('auth::forgot.store'), $payload)
            ->assertRedirect(route('auth::forgot.show'))
            ->assertSessionHas('success');
    }

    #[Test]
    public function it_can_handle_wrong_email()
    {
        $payload = [
            'email' => 'zombie@laravolt.dev',
        ];

        // We must visit form at first, to mimic browser history a.k.a redirect back
        $this->get(route('auth::forgot.show'));

        $this->post(route('auth::forgot.store'), $payload)
            ->assertRedirect(route('auth::forgot.show'))
            ->assertSessionHasErrors('email');
    }

    #[Test]
    public function it_has_errors_if_failed()
    {
        $this->post(route('auth::forgot.store'))->assertSessionHasErrors();
    }

    #[Test]
    public function it_can_handle_send_email_failure()
    {
        $payload = [
            'email' => 'admin@laravolt.dev',
        ];

        $this->instance('laravolt.password', \Mockery::mock(Password::class, function (MockInterface $mock) {
            $mock->shouldReceive('sendResetLink')->once()->andReturn(\Password::RESET_THROTTLED);
        }));

        User::factory()->create($payload);

        $this->post(route('auth::forgot.store'), $payload)
            ->assertRedirect(route('auth::forgot.show'))
            ->assertSessionHas('error');
    }

    #[Test]
    public function it_has_register_link()
    {
        $this->get(route('auth::forgot.show'))->assertSeeText(trans('laravolt::auth.register_here'));
    }

    #[Test]
    public function it_does_not_have_register_link_if_registration_disabled()
    {
        $this->app['config']->set('laravolt.platform.features.registration', false);
        $this->get(route('auth::forgot.show'))->assertDontSeeText(trans('laravolt::auth.register_here'));
    }
}
