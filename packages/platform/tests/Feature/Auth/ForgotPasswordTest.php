<?php

namespace Laravolt\Tests;

class ForgotPasswordTest extends FeatureTest
{
    /**
     * @test
     */
    public function it_can_display_forgot_password_page()
    {
        $this->visitRoute('auth::forgot');
        $this->assertResponseOk();
    }

    /**
     * @test
     */
    public function it_has_forgot_password_form()
    {
        $this->visitRoute('auth::forgot')
             ->seeElement('input[name=email]');
    }

    /**
     * @test
     */
    public function it_can_handle_correct_email()
    {
        $this->visitRoute('auth::forgot')
            ->type('jon@dodo.com', 'email')
            ->press(trans('laravolt::auth.send_reset_password_link'))
            ->seeRouteIs('auth::forgot');
    }

    /**
     * @test
     */
    public function it_can_handle_wrong_email()
    {
        $this->visitRoute('auth::forgot')
             ->type('jon@dodo.com', 'email')
             ->press(trans('laravolt::auth.send_reset_password_link'))
             ->seeRouteIs('auth::forgot');
    }

    /**
     * @test
     */
    public function it_redirect_back_if_failed()
    {
        $this->visitRoute('auth::forgot')
             ->type('invalid-email-format', 'email')
             ->press(trans('laravolt::auth.send_reset_password_link'))
             ->seeRouteIs('auth::forgot');
    }

    /**
     * @test
     */
    public function it_has_errors_if_failed()
    {
        $this->post(route('auth::forgot'))->assertSessionHasErrors();
    }

    /**
     * @test
     */
    public function it_has_register_link()
    {
        $this->get(route('auth::forgot'))->seeText(trans('laravolt::auth.register_here'));
    }

    /**
     * @test
     */
    public function it_does_not_have_register_link_if_registration_disabled()
    {
        $this->app['config']->set('laravolt.auth.registration.enable', false);
        $this->get(route('auth::forgot'))->dontSeeText(trans('laravolt::auth.register_here'));
    }
}
