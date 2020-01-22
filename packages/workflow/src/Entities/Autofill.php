<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Entities;

use Spatie\DataTransferObject\DataTransferObject;

class Autofill extends DataTransferObject
{
    /** @var int|null */
    public $id;

    /** @var string */
    public $column;

    public function __toString()
    {
        return (string) $this->id;
    }
}
