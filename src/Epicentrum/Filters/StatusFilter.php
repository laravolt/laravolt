<?php

namespace Laravolt\Epicentrum\Filters;

use Laravolt\Ui\Filters\CheckboxFilter;

class StatusFilter extends CheckboxFilter
{
    protected string $label = 'Status';

    public function apply($data, $value)
    {
        $status = collect($value)->filter()->keys()->toArray();
        if (! empty($status)) {
            $data->whereIn('status', $status);
        }

        return $data;
    }

    public function options(): array
    {
        return [
            'ACTIVE' => 'ACTIVE',
            'PENDING' => 'PENDING',
        ];
    }
}
