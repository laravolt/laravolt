<?php

declare(strict_types=1);

namespace Laravolt\Camunda\FieldFormatter\Db;

use Laravolt\Camunda\Entities\Multirow;

class MultirowFormatter
{
    public function __invoke($key, $value)
    {
        return new Multirow(['key' => $key, 'data' => $value]);
    }
}
