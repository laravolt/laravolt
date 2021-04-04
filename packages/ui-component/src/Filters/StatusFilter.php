<?php

namespace Laravolt\UiComponent\Filters;

class StatusFilter extends TextFilter
{
    protected string $label = 'Status';

    public function apply($data, $value)
    {
        if ($value) {
            $data->where('status', $value);
        }

        return $data;
    }
}
