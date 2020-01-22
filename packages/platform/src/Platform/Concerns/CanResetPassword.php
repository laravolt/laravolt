<?php

declare(strict_types=1);

namespace Laravolt\Platform\Concerns;

use Illuminate\Support\Facades\Mail;
use Laravolt\Platform\Mail\ResetPasswordLink;

trait CanResetPassword
{
    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     *
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $email = $this->getEmailForPasswordReset();
        Mail::to($email)->send(new ResetPasswordLink($token, $email));
    }
}
