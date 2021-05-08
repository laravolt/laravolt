<?php

declare(strict_types=1);

namespace Laravolt\Workflow\FieldFormatter\Camunda;

use Carbon\Carbon;

class DateFormatter
{
    public function __invoke($key, $value)
    {
        // $format = 'YYYY-MM-DD\THH:MM:SS.SSSZZ';
        $format = 'DD-MM-YYYY';

        return ['value' => Carbon::parse($value)->format('d-m-Y'), 'type' => 'String'];
    }
}
