<?php

declare(strict_types=1);

namespace Laravolt\Workflow\FieldFormatter\Db;

use Laravolt\Workflow\Entities\Multirow;

class MultirowFormatter
{
    public function __invoke($key, $value)
    {
        return new Multirow(['key' => $key, 'data' => $value]);
    }
}
