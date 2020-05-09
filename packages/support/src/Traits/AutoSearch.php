<?php

namespace Laravolt\Support\Traits;

use Illuminate\Database\Eloquent\Builder;

trait AutoSearch
{
    public function scopeAutoSearch(Builder $query, $keyword)
    {
        $query->whereLike($this->searchableColumns, $keyword);
    }
}
