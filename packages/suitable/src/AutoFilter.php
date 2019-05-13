<?php

namespace Laravolt\Suitable;

use Illuminate\Database\Eloquent\Builder;

trait AutoFilter
{
    public function scopeAutoFilter(Builder $query, $filter = 'filter')
    {
        foreach (request($filter, []) as $column => $value) {
            if ($value) {
                $query->whereLike($column, $value);
            }
        }
    }
}
