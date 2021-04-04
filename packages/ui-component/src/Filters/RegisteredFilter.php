<?php

namespace Laravolt\UiComponent\Filters;

use Laravolt\Platform\Models\Role;

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
