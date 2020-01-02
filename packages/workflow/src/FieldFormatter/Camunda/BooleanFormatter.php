<?php

declare(strict_types=1);

namespace Laravolt\Workflow\FieldFormatter\Camunda;

class BooleanFormatter
{
    public function __invoke($key, $value)
    {
        return ['value' => (bool) $value, 'type' => 'Boolean'];
    }
}
