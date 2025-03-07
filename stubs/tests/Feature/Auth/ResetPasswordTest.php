<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected $email = 'fulan@example.com';

    protected $token;

    protected $table;

    protected function setUp(): void
    {
        parent::setUp();

        $this->table = (new User)->getTable();

        $user = User::factory()->create(['email' => $this->email]);
        $this->token = app('auth.password.broker')->createToken($user);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_display_page()
    {
        $this->get(route('auth::reset.show', $this->token))
            ->assertOk()
            ->assertSeeText(__('Email'))
            ->assertSeeText(__('Password'))
            ->assertSeeText(__('Confirm New Password'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_reset_password()
    {
        $payload = [
            'token' => $this->token,
            'email' => $this->email,
            'password' => 'asdf1234',
            'password_confirmation' => 'asdf1234',
        ];
        $this->post(route('auth::reset.store', $this->token), $payload)
            ->assertRedirect(route('auth::login.show'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_handle_failed_passwor_reset()
    {
        $payload = [
            'token' => $this->token,
            'email' => $this->email,
            'password' => 'asdf1234',
            'password_confirmation' => 'asdf1234',
        ];

        \Password::shouldReceive('reset')->andReturn(\Password::RESET_THROTTLED);

        $this->get(route('auth::reset.show', $this->token));
        $this->post(route('auth::reset.store', $this->token), $payload)
            ->assertRedirect(route('auth::reset.show', $this->token))
            ->assertSessionHasErrors('email')
            ->assertSessionHasInput('email');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_has_errors_if_failed()
    {
        $this->post(route('auth::reset.store', 'asdf1234'))->assertSessionHasErrors();
    }
}
