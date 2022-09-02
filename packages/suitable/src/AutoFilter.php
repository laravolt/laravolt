<?php

namespace Laravolt\Suitable;

use Illuminate\Database\Eloquent\Builder;

/** @mixin Builder */
trait AutoFilter
{
    public function scopeAutoFilter(Builder $query, $filter = 'filter')
    {
        // Only apply filter defined in $filterableColumns
        $filterPayload = collect(request($filter, []))->only($this->filterableColumns ?? []);

        foreach ($filterPayload as $column => $value) {
            if ($value) {
                $query->whereLike($column, $value);
            }
        }
    }
}
