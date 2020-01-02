<?php

declare(strict_types=1);

namespace Laravolt\Camunda\FieldFormatter\Db;

use Laravolt\Camunda\Entities\Autofill;

class AutofillFormatter
{
    public function __invoke($key, $value)
    {
        return new Autofill(['id' => (int) $value, 'column' => $key]);
    }
}
