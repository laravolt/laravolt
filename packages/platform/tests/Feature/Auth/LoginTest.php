<?php

namespace Laravolt\Tests;

use Anhskohbo\NoCaptcha\Facades\NoCaptcha;
use Illuminate\Support\Facades\Route;

class LoginTest extends FeatureTest
{
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
    public function it_can_display_login_page()
    {
        $this->get(route('auth::login'));
        $this->assertResponseOk();
    }

    /**
     * @test
     */
    public function it_has_registration_form()
    {
        $this->get(route('auth::login'))
             ->seeElement('input[name=email]')
             ->seeElement('input[name=password]');
    }

    /**
     * @test
     */
    public function it_can_handle_correct_login()
    {
        $this->visitRoute('auth::login')
             ->type('andi@laravolt.com', 'email')
             ->type('asdf1234', 'password')
             ->press('Login')
             ->seeRouteIs('auth::login');
    }

    /**
     * @test
     */
    public function it_can_handle_wrong_login()
    {
        $this->visitRoute('auth::login')
            ->type('wrong@email.com', 'email')
            ->type('wrongpassword', 'password')
            ->press('Login')
            ->seeRouteIs('auth::login');
    }

    /**
     * @test
     */
    public function it_can_handle_correct_login_with_captcha()
    {
        $this->app['config']->set('laravolt.auth.captcha', true);

        NoCaptcha::shouldReceive('display')
                 ->zeroOrMoreTimes()
                 ->andReturn('<input type="hidden" name="g-recaptcha-response" value="1" />');

        NoCaptcha::shouldReceive('renderJs')
                 ->zeroOrMoreTimes();

        NoCaptcha::shouldReceive('verifyResponse')
                 ->once()
                 ->andReturn(true);

        $this->visitRoute('auth::login')
            ->type('andi@laravolt.com', 'email')
            ->type('asdf1234', 'password')
            ->press('Login')
            ->seeRouteIs('auth::login');
    }

    /**
     * @test
     */
    public function it_must_fail_if_captcha_not_checked()
    {
        $this->app['config']->set('laravolt.auth.captcha', true);

        $this->visitRoute('auth::login')
            ->type('andi@laravolt.com', 'email')
            ->type('asdf1234', 'password')
            ->press('Login')
            ->seeRouteIs('auth::login');
    }

    /**
     * @test
     */
    public function it_redirect_back_if_failed()
    {
        $this->visitRoute('auth::login')
            ->type('', 'email')
            ->type('', 'password')
            ->press('Login')
            ->seeRouteIs('auth::login');
    }

    /**
     * @test
     */
    public function it_has_errors_if_failed()
    {
        $this->post(route('auth::login'))->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function it_has_register_link()
    {
        $this->app['config']->set('laravolt.auth.registration.enable', true);

        $this->visitRoute('auth::login')
            ->click(trans('laravolt::auth.register_here'))
            ->seeRouteIs('auth::register');
    }

    /**
     * @test
     */
    public function it_does_not_have_register_link()
    {
        $this->app['config']->set('laravolt.auth.registration.enable', false);

        $this->visitRoute('auth::login')
            ->dontSeeLink(trans('auth:auth.register_here'));
    }

    /**
     * @test
     */
    public function it_has_forgot_password_link()
    {
        $this->visitRoute('auth::login')
            ->click(trans('laravolt::auth.forgot_password'))
            ->seeRouteIs('auth::forgot');
    }

    /**
     * @test
     */
    public function it_can_display_recaptcha()
    {
        $this->app['config']->set('laravolt.auth.captcha', true);

        $this->get(route('auth::login'));

        NoCaptcha::shouldReceive('display')
                 ->zeroOrMoreTimes()
                 ->andReturn('<input type="hidden" name="g-recaptcha-response" value="1" />');
    }
}
