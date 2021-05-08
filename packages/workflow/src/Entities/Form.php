<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Entities;

use Laravolt\Workflow\FieldFormatter\CamundaFormatter;
use Spatie\DataTransferObject\DataTransferObject;

class Form extends DataTransferObject
{
    public array $schema;

    public array | null $data;

    public function toCamundaVariables()
    {
        return CamundaFormatter::format($this->data, $this->schema);
    }
}
