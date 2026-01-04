<?php

declare(strict_types=1);

namespace Laravolt\Mailkeeper;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;

class DbTransport extends AbstractTransport
{
    public function __toString(): string
    {
        return 'db';
    }

    protected function doSend(SentMessage $message): void
    {
        /** @var \Symfony\Component\Mime\Email $originalMessage */
        $originalMessage = $message->getOriginalMessage();
        $contentType = 'text/plain';
        $body = $originalMessage->getTextBody();
        if ($originalMessage->getHtmlBody() !== null) {
            $contentType = 'text/html';
            $body = $originalMessage->getHtmlBody();
        }

        $data = [
            'from' => $this->normalizeAddresses($originalMessage->getFrom()),
            'sender' => $originalMessage->getSender(),
            'to' => $this->normalizeAddresses($originalMessage->getTo()),
            'cc' => $this->normalizeAddresses($originalMessage->getCc()),
            'bcc' => $this->normalizeAddresses($originalMessage->getBcc()),
            'reply_to' => $this->normalizeAddresses($originalMessage->getReplyTo()),
            'priority' => $originalMessage->getPriority(),
            'content_type' => $contentType,
            'body' => $body,
            'subject' => $originalMessage->getSubject(),
        ];

        Mail::create($data);
    }

    protected function normalizeAddresses(array $addresses): array
    {
        return collect($addresses)
            ->transform(callback: fn (Address $address) => $address->getAddress())
            ->toArray();
    }
}
