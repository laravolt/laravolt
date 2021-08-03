<?php

namespace Laravolt\Mailkeeper;

use Illuminate\Mail\MailManager;

class DbMailManager extends MailManager
{
    public function createTransport(array $config)
    {
        return new DbTransport();
    }
}
