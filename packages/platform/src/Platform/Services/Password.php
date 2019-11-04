<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravolt\Contracts\CanChangePassword;
use Laravolt\Platform\Mail\NewPasswordInformation;
use UnexpectedValueException;

class Password
{
    protected $token;

    protected $mailer;

    /**
     * Password constructor.
     *
     * @param TokenRepositoryInterface $token
     * @param Mailer                   $mailer
     */
    public function __construct(TokenRepositoryInterface $token, Mailer $mailer)
    {
        $this->token = $token;
        $this->mailer = $mailer;
    }

    public function sendResetLink(CanResetPassword $user)
    {
        $user->sendPasswordResetNotification($this->token->create($user));

        return PasswordBroker::RESET_LINK_SENT;
    }

    /**
     * @param CanChangePassword $user
     * @param bool|false        $mustBeChanged
     *
     * @return bool
     */
    public function sendNewPassword(CanChangePassword $user, $mustBeChanged = false)
    {
        $newPassword = $this->generate();
        $user->setPassword($newPassword, $mustBeChanged);

        $this->emailNewPassword($user, $newPassword);

        return true;
    }

    public function changePasswordByToken($user, $password, $token)
    {
        if (!$user instanceof CanResetPassword) {
            throw new UnexpectedValueException('User must implement CanResetPassword interface.');
        }

        if (!$user instanceof CanChangePassword) {
            throw new UnexpectedValueException('User must implement CanChangePasswordContract interface.');
        }

        if (!$this->token->exists($user, $token)) {
            return \Illuminate\Support\Facades\Password::INVALID_TOKEN;
        }

        return DB::transaction(function () use ($user, $password) {
            $user->setPassword($password);
            $this->token->delete($user);

            return \Illuminate\Support\Facades\Password::PASSWORD_RESET;
        });
    }

    protected function generate($length = 8)
    {
        return Str::random($length);
    }

    protected function emailNewPassword(CanChangePassword $user, $password)
    {
        $email = $user->getEmailForNewPassword();
        Mail::to($email)->send(new NewPasswordInformation($password));
    }
}
