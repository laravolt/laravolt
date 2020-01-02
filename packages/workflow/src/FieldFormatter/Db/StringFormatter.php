<?php

declare(strict_types=1);

namespace Laravolt\Workflow\FieldFormatter\Db;

class StringFormatter
{
    public function __invoke($key, $value)
    {
        if ($value === null) {
            return null;
        }

        return (string) $value;
    }
}
