<?php

declare(strict_types=1);

namespace Laravolt\Workflow\FieldFormatter\Db;

use Laravolt\Workflow\Entities\Autofill;

class AutofillFormatter
{
    public function __invoke($key, $value)
    {
        return new Autofill(['id' => (int) $value, 'column' => $key]);
    }
}
