<?php

namespace Laravolt\Mailkeeper;

use Illuminate\Mail\Transport\Transport;
use Swift_Mime_SimpleMessage;

class DbTransport extends Transport
{
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $data = [
            'from'         => $message->getFrom(),
            'sender'       => $message->getSender(),
            'to'           => $message->getTo(),
            'cc'           => $message->getCc(),
            'bcc'          => $message->getBcc(),
            'reply_to'     => $message->getReplyTo(),
            'priority'     => $message->getPriority(),
            'content_type' => $message->getBodyContentType(),
            'body'         => html_entity_decode($message->getBody()),
            'subject'      => $message->getSubject() ?? 'No Subject',
        ];

        Mail::create($data);
    }
}
