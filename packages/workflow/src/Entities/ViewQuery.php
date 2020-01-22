<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Entities;

use Spatie\DataTransferObject\DataTransferObject;

class ViewQuery extends DataTransferObject
{
    /** @var string */
    public $name;

    public $query;

    public static function make($name, $query): self
    {
        return new self([
            'name' => $name,
            'query' => $query,
        ]);
    }
}
