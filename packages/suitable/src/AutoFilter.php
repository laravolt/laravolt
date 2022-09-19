<?php

namespace Laravolt\Suitable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/** @mixin Builder */
trait AutoFilter
{
    public function scopeAutoFilter(Builder $query, $filter = 'filter')
    {
        // Only apply filter defined in $filterableColumns
        $filterPayload = collect(request($filter, []))->only($this->filterableColumns ?? []);

        foreach ($filterPayload as $column => $value) {
            if (Str::contains($column, '.')) {
                $relationName = Str::beforeLast($column, '.');
                $column = Str::afterLast($column, '.');

                /** @phpstan-ignore-next-line */
                $query->leftJoinRelationship($relationName);

                // Get fully qualified column name
                $lastRelationName = Str::afterLast($relationName, '.');
                $relatedTable = $this->$lastRelationName()->getModel()->getTable();
                $column = "$relatedTable.$column";
            }

            if (is_string($value)) {
                $query->where($column, $value);
            } elseif (is_array($value)) {
                $query->whereIn($column, Arr::flatten($value));
            }
        }
    }
}
