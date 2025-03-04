<?php

declare(strict_types=1);

namespace Laravolt\Support;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravolt\Support\Mixin\QueryBuilderMixin;
use Laravolt\Support\Mixin\StrMixin;

class SupportServiceProvider extends ServiceProvider
{
    public function register() {}

    public function boot()
    {
        $this->registerMacro();
    }

    protected function registerMacro()
    {
        Builder::mixin(new QueryBuilderMixin);
        Str::mixin(new StrMixin);

        EloquentBuilder::macro('whereLike', function ($attributes, ?string $searchTerm) {
            if ($searchTerm === null || trim($searchTerm) === '') {
                return $this;
            }

            $searchTerm = trim(DB::getPdo()->quote((strtolower($searchTerm))), "'");
            $this->where(function (EloquentBuilder $query) use ($attributes, $searchTerm) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        Str::contains($attribute, '.'),
                        function (EloquentBuilder $query) use ($attribute, $searchTerm) {
                            [$relationName, $relationAttribute] = explode('.', $attribute);

                            $query->orWhereHas(
                                $relationName,
                                function (EloquentBuilder $query) use ($relationAttribute, $searchTerm) {
                                    $query->whereRaw(sprintf(
                                        "LOWER(%s) LIKE '%%%s%%'",
                                        $relationAttribute,
                                        $searchTerm
                                    ));
                                }
                            );
                        },
                        function (EloquentBuilder $query) use ($attribute, $searchTerm) {
                            $table = $query->getModel()->getTable();
                            if (Str::contains($attribute, '->')) {
                                $query->orWhere($attribute, 'ilike', "%$searchTerm%");
                            } else {
                                $query->orWhereRaw(sprintf(
                                    "LOWER(%s.%s) LIKE '%%%s%%'",
                                    $table,
                                    $attribute,
                                    $searchTerm
                                ));
                            }
                        }
                    );
                }
            });

            return $this;
        });
    }
}
