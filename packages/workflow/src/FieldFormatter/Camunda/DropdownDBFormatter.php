<?php

declare(strict_types=1);

namespace Laravolt\Workflow\FieldFormatter\Camunda;

class DropdownDBFormatter
{
    public function __invoke($key, $values)
    {
        if (! is_array($values)) {
            $values = [];
        }

        return ['value' => array_values($values)];
    }
}
