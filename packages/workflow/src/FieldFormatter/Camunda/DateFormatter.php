<?php

declare(strict_types=1);

namespace Laravolt\Workflow\FieldFormatter\Camunda;

use Carbon\Carbon;

class DateFormatter
{
    private string $format = 'YYYY-MM-DD\THH:MM:SS.SSSZZ';

    public function __invoke($key, $value)
    {
        return ['value' => Carbon::parse($value)->isoFormat($this->format), 'type' => 'Date'];
    }
}
