<?php

namespace Laravolt\Suitable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Kirschbaum\PowerJoins\PowerJoins;

trait AutoSort
{
    use PowerJoins;

    public function scopeAutoSort(Builder $query, $sortByKey = null, $sortDirectionKey = null)
    {
        $sortByKey = $sortByKey ?? config('suitable.query_string.sort_by');
        $sortDirectionKey = $sortDirectionKey ?? config('suitable.query_string.sort_direction');
        $direction = request()->get($sortDirectionKey, 'asc');

        if (request()->has($sortByKey)) {
            $columnQueryString = $column = request()->get($sortByKey);
            if (Str::contains($columnQueryString, '.')) {
                $relationName = Str::beforeLast($columnQueryString, '.');
                $query->select($this->getTable().'.*');
                $query->leftJoinRelationship($relationName);
                $query->orderByPowerJoins($columnQueryString, $direction);
            } else {
                $query->orderBy($column, $direction);
            }
        }
    }
}
