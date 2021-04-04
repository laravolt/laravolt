<?php

namespace Laravolt\UiComponent\Filters;

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
