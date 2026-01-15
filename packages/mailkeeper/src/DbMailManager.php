<?php

declare(strict_types=1);

namespace Laravolt\Mailkeeper;

use Illuminate\Mail\MailManager;

class DbMailManager extends MailManager
{
    /**
     * @return LegacyDbTransport
     *
     * @deprecated
     */
    public function createTransport(array $config)
    {
        return new LegacyDbTransport;
    }

    public function createSymfonyTransport(array $config)
    {
        return new DbTransport;
    }
}
