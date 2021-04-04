<?php

namespace Laravolt\UiComponent\Filters;

use Laravolt\Platform\Models\Role;

class EmailFilter extends TextFilter
{
    protected string $label = 'Email';

    public function apply($data, $value)
    {
        if ($value) {
            $data->where('email', $value);
        }

        return $data;
    }
}
