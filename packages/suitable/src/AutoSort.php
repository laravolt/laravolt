<?php

namespace Laravolt\Suitable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait AutoSort
{
    public function scopeAutoSort(Builder $query, $payload = null, $sortByKey = null, $sortDirectionKey = null)
    {
        if ($payload === null) {
            $payload = request()->all();
        }

        $payload = collect($payload);

        $sortByKey = $sortByKey ?? config('suitable.query_string.sort_by');
        $sortDirectionKey = $sortDirectionKey ?? config('suitable.query_string.sort_direction');

        $direction = Arr::get($payload, $sortDirectionKey, 'asc');

        // support for AlurKerja sort format
        if ($payload->has('asc')) {
            $direction = filter_var($payload->get('asc'), FILTER_VALIDATE_BOOLEAN) ? 'asc' : 'desc';
        }

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
