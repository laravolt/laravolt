<?php

declare(strict_types=1);

namespace Laravolt\Camunda\FieldFormatter\Camunda;

class StringFormatter
{
    public function __invoke($key, $value)
    {
        return ['value' => (string) $value, 'type' => 'String'];
    }
}
