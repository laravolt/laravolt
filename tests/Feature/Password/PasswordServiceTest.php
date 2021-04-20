<?php

declare(strict_types=1);

namespace Laravolt\Tests\Feature\Password;

use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Laravolt\Platform\Mail\NewPasswordInformation;
use Laravolt\Platform\Mail\ResetPasswordLink;
use Laravolt\Tests\FeatureTest;

class PasswordServiceTest extends FeatureTest
{
    public function testSendResetLink()
    {
        Mail::fake();
        $service = app('laravolt.password');
        $user = $this->createUser();

        $service->sendResetLink($user);

        Mail::assertSent(ResetPasswordLink::class, function (Mailable $mail) use ($user) {
            return $mail->hasTo($user->getEmailForPasswordReset());
        });
    }

    public function testSendNewPassword()
    {
        Mail::fake();
        $service = app('laravolt.password');
        $user = $this->createUser();

        $service->sendNewPassword($user, false);

        Mail::assertSent(NewPasswordInformation::class, function (Mailable $mail) use ($user) {
            return $mail->hasTo($user->getEmailForPasswordReset());
        });
    }

    public function testChangePasswordByInvalidToken()
    {
        $service = app('laravolt.password');
        $user = $this->createUser();

        $token = 'asdf1234';
        $response = $service->changePasswordByToken($user, 'secret2', $token);
        $this->assertSame(Password::INVALID_TOKEN, $response);
    }

    public function testChangePasswordByValidToken()
    {
        $token = 'asdf1234';
        $user = $this->createUser();

        $tokenRepo = \Mockery::mock(TokenRepositoryInterface::class);
        $tokenRepo->shouldReceive('create')->andReturn($token);
        $tokenRepo->shouldReceive('exists')->andReturn(true);
        $tokenRepo->shouldReceive('delete');

        $mailer = Mail::fake();

        $service = new \Laravolt\Platform\Services\Password($tokenRepo, $mailer);
        $service->sendResetLink($user);

        $response = $service->changePasswordByToken($user, 'secret2', $token);
        $this->assertSame(Password::PASSWORD_RESET, $response);
        $this->assertTrue(Hash::check('secret2', $user->password));
    }
}
