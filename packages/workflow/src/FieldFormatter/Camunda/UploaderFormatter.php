<?php

declare(strict_types=1);

namespace Laravolt\Workflow\FieldFormatter\Camunda;

class UploaderFormatter
{
    public function __invoke($key, $value)
    {
        return ['value' => request()->media($key)->toJson(), 'type' => 'String'];
    }
}
