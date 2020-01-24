<?php

declare(strict_types=1);

namespace Laravolt\Support\Mixin;

use Illuminate\Database\Query\Builder;

class QueryBuilderMixin
{
    public function firstOrFail()
    {
        return function () {
            $result = $this->first();
            if (!$result) {
                abort(404);
            }

            return $result;
        };
    }

    public function whereLike()
    {
        return function ($attributes, ?string $searchTerm) {
            if ($searchTerm) {
                $searchTerm = strtolower($searchTerm);
                $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                    foreach (Arr::wrap($attributes) as $column) {
                        $query->orWhereRaw(sprintf("LOWER(%s) LIKE '%%%s%%'", $column, $searchTerm));
                    }
                });
            }

            return $this;
        };
    }

    public function autoFilter()
    {
        return function () {
            foreach (request('filter', []) as $column => $value) {
                if ($value) {
                    $this->whereLike($column, $value);
                }
            }

            return $this;
        };
    }

    public function autoSort()
    {
        return function ($sortByKey = 'sort', $sortDirectionKey = 'direction') {
            $direction = request()->get($sortDirectionKey, 'asc');

            if (request()->has($sortByKey)) {
                $column = request()->get($sortByKey);
                $this->orderBy($column, $direction);
            }

            return $this;
        };
    }
}
