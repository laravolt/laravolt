<?php

namespace Laravolt\Epicentrum\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountInformation extends Mailable
{
    use Queueable;
    use SerializesModels;
    public $user;

    public $plainPassword;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param $plainPassword
     */
    public function __construct($user, $plainPassword)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('epicentrum::emails.account_information');
    }
}
