<?php

namespace Laravolt\Suitable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/** @mixin Builder */
trait AutoFilter
{
    private function isJsonColumn($castField, $column)
    {
        $jsonFieldName = $column;
        if (Str::contains($column, '.')) {
            $jsonFieldName = Str::beforeLast($column, '.');
        }

        // Filter item, with key == column  and value is array
        $found = collect($castField)->filter(function ($value, $key) use ($jsonFieldName) {
            return $key === $jsonFieldName && $value === "array";
        });

        return sizeof($found) > 0;
    }

    public function scopeAutoFilter(Builder $query, $filter = 'filter')
    {
        // Only apply filter defined in $filterableColumns
        $filterPayload = collect(request($filter, []))->only($this->filterableColumns ?? []);

        $castField = $this->casts ?: [];  // default kosong takut undefined
        foreach ($filterPayload as $column => $value) {
            if ($this->isJsonColumn($castField, $column)) {
                $column = str_replace(".", "->", (string)$column);
                $query->whereJsonContains($column, $value);
                continue;
            }

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

            if (is_string($value) || is_null($value)) {
                if ($value === ($this->filterNotNull ?? '(filled)')) {
                    $query->whereNotNull($column);
                } else {
                    $query->where($column, $value);
                }
            } elseif (is_array($value)) {
                $query->whereIn($column, Arr::flatten($value));
            }
        }
    }
}
