<?php

declare(strict_types=1);

namespace Laravolt\Workflow\FieldFormatter\Camunda;

class MultirowFormatter
{
    public function __invoke($key, $value)
    {
        return ['value' => json_encode($value), 'type' => 'String'];
    }
}
