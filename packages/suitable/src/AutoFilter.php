<?php

namespace Laravolt\Suitable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/** @mixin Builder */
trait AutoFilter
{
    public function scopeAutoFilter(Builder $query, $filter = 'filter')
    {
        // Only apply filter defined in $filterableColumns
        $filterPayload = collect(request($filter, []))->only($this->filterableColumns ?? []);

        foreach ($filterPayload as $column => $value) {
            if (is_string($value)) {
                $query->whereLike($column, $value);
            } elseif (is_array($value)) {
                $query->whereIn($column, Arr::flatten($value));
            }
        }
    }
}
