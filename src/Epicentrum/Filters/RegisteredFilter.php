<?php

namespace Laravolt\Epicentrum\Filters;

use Laravolt\UiComponent\Filters\DateFilter;

class RegisteredFilter extends DateFilter
{
    protected string $label = 'Registered At';

    public function apply($data, $value)
    {
        if ($value) {
            $data->where('created_at', $value);
        }

        return $data;
    }
}
