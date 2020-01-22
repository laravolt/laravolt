<?php

declare(strict_types=1);

namespace Laravolt\Platform\Mail;

use Illuminate\Mail\Mailable;

class ResetPasswordLink extends Mailable
{
    public $token;

    public $email;

    /**
     * ResetLinkMail constructor.
     */
    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function build()
    {
        return $this->subject(trans('laravolt::password.reset_link_mail_subject'))
            ->view('laravolt::emails.reset-password-link');
    }
}
