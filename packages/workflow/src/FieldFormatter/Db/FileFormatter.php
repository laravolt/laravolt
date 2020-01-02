<?php

declare(strict_types=1);

namespace Laravolt\Camunda\FieldFormatter\Db;

class FileFormatter
{
    public function __invoke($key, $value)
    {
        return request()->media($key)->toJson();
    }
}
