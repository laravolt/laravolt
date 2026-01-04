<?php

declare(strict_types=1);

namespace Laravolt\Epicentrum\Filters;

use Carbon\Carbon;
use Laravolt\Ui\Filters\DateFilter;

class RegisteredFilter extends DateFilter
{
    protected string $label = 'Registered At';

    public function apply($data, $value)
    {
        if ($value) {
            $data->whereDate('created_at', Carbon::parse($value)->toDateString());
        }

        return $data;
    }
}
