<?php

namespace Laravolt\Epicentrum\Filters;

use Laravolt\Ui\Filters\DateFilter;
use Carbon\Carbon;

class RegisteredFilter extends DateFilter
{
    protected string $label = 'Registered At';

    public function apply($data, $value)
    {
        if ($value) {
            $data->whereDate('created_at',  Carbon::parse($value)->toDateString());
        }

        return $data;
    }
}
