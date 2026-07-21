<?php

declare(strict_types=1);

namespace Laravolt\Support\Mixin;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;

class QueryBuilderMixin
{
    public function firstOrFail()
    {
        return function () {
            $result = $this->first();
            if (! $result) {
                abort(404);
            }

            return $result;
        };
    }

    public function autoFilter()
    {
        return function () {
            // NOTE: Laravel 12+ ships a native Query\Builder::whereLike() which takes
            // precedence over any mixin method of the same name, and it does NOT wrap
            // the term in wildcards. We inline the substring-search behavior here so
            // ?filter[name]=john keeps matching partially and case-insensitively.
            foreach (request('filter', []) as $column => $value) {
                if ($value !== null && mb_trim((string) $value) !== '') {
                    $searchTerm = mb_strtolower(mb_trim((string) $value));
                    $this->where(function (Builder $query) use ($column, $searchTerm) {
                        foreach (Arr::wrap($column) as $col) {
                            $query->orWhereRaw(
                                sprintf('LOWER(%s) LIKE ?', $query->getGrammar()->wrap($col)),
                                ["%$searchTerm%"]
                            );
                        }
                    });
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
