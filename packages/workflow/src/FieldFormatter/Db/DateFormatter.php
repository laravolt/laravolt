<?php

declare(strict_types=1);

namespace Laravolt\Camunda\FieldFormatter\Db;

class DateFormatter
{
    public function __invoke($key, $value)
    {
        return $value;
    }
}
