<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::get('login-success', function () {
            return 'login success';
        });
    }

    #[Test]
    public function it_can_display_login_page()
    {
        $this->get(route('auth::login.show'))
            ->assertOk()
            ->assertSeeText('Email')
            ->assertSeeText('Password');
    }

    #[Test]
    public function it_can_handle_correct_login()
    {
        $payload = [
            'email' => 'admin@laravolt.dev',
            'status' => 'ACTIVE',
        ];

        User::factory()->create($payload + ['password' => bcrypt('asdf1234')]);
        RateLimiter::shouldReceive('tooManyAttempts')->andReturnFalse();
        RateLimiter::shouldReceive('availableIn')->andReturn(3);
        RateLimiter::shouldReceive('clear');

        $response = $this->post(route('auth::login.store'), $payload + ['password' => 'asdf1234']);

        $response->assertRedirect(AppServiceProvider::HOME);

        $this->get(AppServiceProvider::HOME)->assertSee('Home');
    }

    #[Test]
    public function it_can_handle_wrong_login()
    {
        $payload = [
            'email' => 'admin@laravolt.dev',
        ];

        User::factory()->create($payload + ['password' => bcrypt('asdf1234')]);

        $this->get(route('auth::login.show'));
        $response = $this->post(route('auth::login.store'), $payload + ['password' => 'wrong-password']);

        $response->assertRedirect(route('auth::login.show'));
    }

    #[Test]
    public function ensure_password_required()
    {
        $this->post(route('auth::login.store'), ['email' => 'user@laravolt.dev'])
            ->assertSessionHasErrors('password');
    }

    #[Test]
    public function it_has_errors_if_failed()
    {
        $this->post(route('auth::login.store'))->assertSessionHasErrors();
    }

    #[Test]
    public function it_has_register_link()
    {
        $this->app['config']->set('laravolt.platform.features.registration', true);

        $this->get(route('auth::login.show'))
            ->assertSeeText(trans('laravolt::auth.register_here'));
    }

    #[Test]
    public function it_does_not_have_register_link()
    {
        $this->app['config']->set('laravolt.platform.features.registration', false);

        $this->get(route('auth::login.show'))
            ->assertDontSeeText(trans('laravolt::auth.register_here'));
    }

    #[Test]
    public function it_has_forgot_password_link()
    {
        $this->get(route('auth::login.show'))
            ->assertSeeText(trans('laravolt::auth.forgot_password'));
    }

    #[Test]
    public function ensure_rate_limiter()
    {
        $limit = 5;
        $payload = [
            'email' => 'admin@laravolt.dev',
            'password' => 'etalazen',
        ];

        for ($i = 0; $i < $limit; $i++) {
            $this->post(route('auth::login.store'), $payload);
        }

        $lastRequest = $this->post(route('auth::login.store'), $payload);
        $lastRequest->assertSessionHasErrors('email');
    }
}
