<?php

namespace Laravolt\Suitable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Kirschbaum\PowerJoins\PowerJoins;

trait AutoSort
{
    use PowerJoins;

    public function scopeAutoSort(Builder $query, $sortByKey = null, $sortDirectionKey = null, $payload = null)
    {
        if ($payload === null) {
            $payload = request();
        }

        $sortByKey = $sortByKey ?? config('suitable.query_string.sort_by');
        $sortDirectionKey = $sortDirectionKey ?? config('suitable.query_string.sort_direction');
        $direction = Arr::get($payload, $sortDirectionKey, 'asc');

        if (Arr::get($payload, $sortByKey)) {
            $columnQueryString = $column = Arr::get($payload, $sortByKey);
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
