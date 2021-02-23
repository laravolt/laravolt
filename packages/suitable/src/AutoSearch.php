<?php

namespace Laravolt\Suitable;

use Illuminate\Database\Eloquent\Builder;

/** @mixin Builder */
trait AutoSearch
{
    public function scopeAutoSearch(Builder $query, ?string $keyword)
    {
        if ($keyword !== null) {
            $query->whereLike($this->searchableColumns, $keyword);
        }
    }
}
