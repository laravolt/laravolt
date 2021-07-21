<?php

declare(strict_types=1);

namespace Laravolt\Workflow\FieldFormatter\Camunda;

class DropdownDBFormatter
{
    public function __invoke($key, $values)
    {
        return ['value' => array_values($values), 'type' => 'String'];
    }
}
