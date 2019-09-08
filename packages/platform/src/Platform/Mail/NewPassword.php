<?php

declare(strict_types=1);

namespace Laravolt\Platform\Mail;

use Illuminate\Mail\Mailable;

class NewPassword extends Mailable
{
    public $password;

    /**
     * ResetLinkMail constructor.
     */
    public function __construct($password)
    {
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject(__('laravolt::password.new_password_mail_subject'))
            ->view(config('laravolt.password.emails.new'));
    }
}
