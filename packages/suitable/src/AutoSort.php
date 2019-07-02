<?php

namespace Laravolt\Suitable;

use Illuminate\Database\Eloquent\Builder;

trait AutoSort
{
    public function scopeAutoSort(Builder $query, $sortByKey = null, $sortDirectionKey = null)
    {
        $sortByKey = $sortByKey ?? config('suitable.query_string.sort_by');
        $sortDirectionKey = $sortDirectionKey ?? config('suitable.query_string.sort_direction');
        $direction = request()->get($sortDirectionKey, 'asc');

        if (request()->has($sortByKey)) {
            $column = request()->get($sortByKey);
            if (str_contains($column, '.')) {
                $temp = explode('.', $column);
                $relation = $this->{$temp[0]}();
                $related = $relation->getRelated();
                $table = $related->getTable();
                $column = $temp[1];

                $query->select($this->getTable().'.*');
                $query->join($table, $relation->getForeignKey(), '=', $table.".".$relation->getOwnerKey());
                $query->orderBy($table.".".$column, $direction);
            } else {
                $query->orderBy($column, $direction);
            }
        }
    }
}
