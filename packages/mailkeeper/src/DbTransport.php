<?php

namespace Laravolt\Mailkeeper;

use Illuminate\Mail\Transport\Transport;
use Swift_Mime_SimpleMessage;

class DbTransport extends Transport
{
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        foreach ($message->getTo() as $toEmail => $toName) {
            $data = [
                'from'    => $message->getFrom(),
                'to'      => [$toEmail => $toName],
                'body'    => $message->getBody(),
                'subject' => $message->getSubject(),
            ];

            Mail::create($data);
        }
    }
}
