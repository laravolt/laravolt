<?php

namespace Laravolt\Support\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait AutoSort
{
    public function scopeAutoSort(Builder $query, $sortByKey = 'sort', $sortDirectionKey = 'direction')
    {
        $direction = request()->get($sortDirectionKey, 'asc');

        if (request()->has($sortByKey)) {
            $column = request()->get($sortByKey);
            if (Str::contains($column, '.')) {
                $temp = explode('.', $column);
                $relation = $this->{$temp[0]}();
                $related = $relation->getRelated();
                $table = $related->getTable();
                $column = $temp[1];

                $foreignKey = $relation->getQualifiedForeignKeyName();
                $ownerKey = $relation->getQualifiedOwnerKeyName();

                $query->select($this->getTable().'.*');
                $query->join($table, $foreignKey, '=', $ownerKey);
                $query->orderBy($table.'.'.$column, $direction);
            } else {
                $query->orderBy($column, $direction);
            }
        }
    }
}
