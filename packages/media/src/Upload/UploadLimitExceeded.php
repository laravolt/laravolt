<?php

declare(strict_types=1);

namespace Laravolt\Media\Upload;

use RuntimeException;

/**
 * A chunked upload went over a configured limit. The status is the HTTP code
 * the endpoint answers with.
 */
class UploadLimitExceeded extends RuntimeException
{
    public function __construct(string $message, public readonly int $status)
    {
        parent::__construct($message);
    }
}
