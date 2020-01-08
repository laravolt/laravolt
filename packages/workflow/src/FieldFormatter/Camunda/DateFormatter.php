<?php

declare(strict_types=1);

namespace Laravolt\Workflow\FieldFormatter\Camunda;

use Carbon\Carbon;

class DateFormatter
{
    public function __invoke($key, $value)
    {
        return ['value' => Carbon::parse($value)->isoFormat('YYYY-MM-DD\THH:MM:SS.SSSZZ'), 'type' => 'Date'];
    }
}
