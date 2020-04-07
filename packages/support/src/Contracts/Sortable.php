<?php

namespace Laravolt\Support\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Sortable
{
    /**
     * Move current model position to the beginning.
     */
    public function moveToFirst(): void;

    /**
     * Move current model position to the last.
     */
    public function moveToLast(): void;

    /**
     * Move to specific position.
     */
    public function moveToPosition(int $position): void;

    /**
     * Move current model position before other $model.
     */
    public function moveBefore(self $model): void;

    /**
     * Move current model position after other $model.
     */
    public function moveAfter(self $model): void;

    /**
     * Move current model position after other $model.
     */
    public function reposition(): void;

    /**
     * Get current position.
     */
    public function getPosition(): ?int;

    /**
     * Let's be nice and provide an sorted scope.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeOrderByPosition(Builder $query);

    /**
     * This function reorders the records: the record with the first id in the array
     * will get order 1, the record with the second it will get order 2,...
     *
     * @param array|\ArrayAccess $ids
     * @param int                $startPosition
     */
    public static function sort($ids, int $startPosition = 1);
}
