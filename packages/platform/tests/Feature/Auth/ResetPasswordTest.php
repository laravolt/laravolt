<?php

namespace Laravolt\Tests;

use Illuminate\Support\Facades\Route;
use Laravolt\Platform\Models\User;

class ResetPasswordTest extends FeatureTest
{
    protected $email = 'fulan@example.com';

    protected $token;

    protected $table;

    public function setUp(): void
    {
        parent::setUp();

        $this->createUser();

        $this->table = config('auth.passwords.users.table');

        $user = User::whereEmail($this->email)->first();
        $this->token = app('auth.password.broker')->createToken($user);
    }

    /**
     * @test
     */
    public function it_can_display_page()
    {
        $this->visitRoute('auth::reset', 'asdf1234');
        $this->assertResponseOk();
    }

    /**
     * @test
     */
    public function it_has_correct_form_field()
    {
        $this->visitRoute('auth::reset', 'asdf1234')
            ->seeElement('input[name=email]')
            ->seeElement('input[name=password]')
            ->seeElement('input[name=password_confirmation]');
    }

    /**
     * @test
     */
    public function it_can_reset_password()
    {
        $this->app['config']->set('laravolt.auth.password.reset.auto_login', true);

        Route::get('home', function () {
            return 'login success';
        })->name('home');

        $this->visitRoute('auth::reset', $this->token)
            ->type($this->email, 'email')
            ->type('1nd0n351a r4y4', 'password')
            ->type('1nd0n351a r4y4', 'password_confirmation')
            ->press(trans('laravolt::auth.reset_password'))
            ->seePageIs('home');

        $this->seeIsAuthenticated();
    }

    /**
     * @test
     */
    public function it_can_reset_password_and_redirect_to_login_page()
    {
        $this->app['config']->set('laravolt.auth.password.reset.auto_login', false);
        $this->app['config']->set('laravolt.auth.redirect.after_reset_password', '/auth/login');

        $this->visitRoute('auth::reset', $this->token)
            ->type($this->email, 'email')
            ->type('1nd0n351a r4y4', 'password')
            ->type('1nd0n351a r4y4', 'password_confirmation')
            ->press(trans('laravolt::auth.reset_password'))
            ->seeRouteIs('auth::login');
    }

    /**
     * @test
     */
    public function it_redirect_back_if_failed()
    {
        $this->visitRoute('auth::reset', 'asdf1234')
            ->type('invalid-email-format', 'email')
            ->press(trans('laravolt::auth.reset_password'))
            ->seeRouteIs('auth::reset', 'asdf1234');
    }

    /**
     * @test
     */
    public function it_has_errors_if_failed()
    {
        $this->post(route('auth::reset', 'asdf1234'))->assertSessionHasErrors();
    }
}
