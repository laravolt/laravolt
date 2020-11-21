<?php

namespace Laravolt\Support\Traits;

use ArrayAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Laravolt\Support\Contracts\Sortable;

trait SortableTrait
{
    protected $previousPosition;

    public static function bootSortableTrait()
    {
        static::creating(
            function (Model $model) {
                if ($model->getPosition() === null) {
                    $model->setPositionAttribute($model->getLastPosition() + 1);
                }

                // If there are another models with same position,
                // increment theirs position by 1
                if ($model->buildSortableQuery()
                        ->where($model->getSortableField(), $model->getPosition())
                        ->count() >= 1) {
                    $model->buildSortableQuery()
                        ->whereKeyNot($model->getKey())
                        ->where('order', '>=', $model->getPosition())
                        ->increment('order');
                }
            }
        );

        static::updated(
            function (Sortable $model) {
                $model->reposition();
            }
        );
    }

    /**
     * Get current position.
     */
    public function getPosition(): ?int
    {
        return $this->{$this->getSortableField()};
    }

    /**
     * Determine the order value for the new record.
     */
    public function getLastPosition(): int
    {
        return (int) $this->buildSortableQuery()->max($this->getSortableField());
    }

    /**
     * Let's be nice and provide an ordered scope.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $direction
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeOrderByPosition(Builder $query, string $direction = 'asc')
    {
        return $query->orderBy($this->getSortableField(), $direction);
    }

    /**
     * This function reorders the records: the record with the first id in the array
     * will get order 1, the record with the second it will get order 2, ...
     * A starting order number can be optionally supplied (defaults to 1).
     *
     * @param array|\ArrayAccess $ids
     * @param int                $startPosition
     */
    public static function sort($ids, int $startPosition = 1)
    {
        if (! is_array($ids) && ! $ids instanceof ArrayAccess) {
            throw new InvalidArgumentException('You must pass an array or ArrayAccess object to setNewOrder');
        }

        foreach ($ids as $id) {
            if ($id instanceof Model) {
                $id = $id->getKey();
            }
            static::withoutGlobalScope(SoftDeletingScope::class)
                ->whereKey($id)
                ->update([static::$sortable['column'] => $startPosition++]);
        }
    }

    /**
     * Move current model position to the beginning.
     */
    public function moveToFirst(): void
    {
        $this->moveToPosition(1);
    }

    /**
     * Move current model position to the last.
     */
    public function moveToLast(): void
    {
        $this->moveToPosition($this->getLastPosition());
    }

    /**
     * Move current model position before other $model.
     */
    public function moveBefore(Sortable $model): void
    {
        $position = $model->getPosition();
        $delta = $position - $this->getPosition();

        // move up
        if ($delta > 0) {
            $position -= 1;
        }

        $this->moveToPosition($position);
    }

    /**
     * Move current model position after other $model.
     */
    public function moveAfter(Sortable $model): void
    {
        $position = $model->getPosition();
        $delta = $position - $this->getPosition();

        // move down
        if ($delta < 0) {
            $position += 1;
        }

        $this->moveToPosition($position);
    }

    /**
     * Move to specific position.
     */
    public function moveToPosition(int $position): void
    {
        if ($position == $this->getPosition()) {
            return;
        }

        // Make sure position in between 1 and highest position
        $position = max(1, min($position, $this->getLastPosition()));

        DB::transaction(
            function () use ($position) {
                $delta = $position - $this->getPosition();

                // move up
                if ($delta > 0) {
                    $this->buildSortableQuery()
                        ->whereBetween('order', [$this->getPosition(), $position])
                        ->decrement('order');
                } else {
                    $this->buildSortableQuery()
                        ->whereBetween('order', [$position, $this->getPosition() - 1])
                        ->increment('order');
                }

                $this->update(['order' => $position]);
            }
        );
    }

    public function reposition(): void
    {
        $delta = $this->getPosition() - $this->previousPosition;

        // Decide whether model instance move up ($delta > 0) or move down ($delta < 0),
        // so we can reorder sibling fields correctly
        if ($delta > 0) {
            $this->buildSortableQuery()
                ->whereBetween('order', [$this->previousPosition, $this->getPosition()])
                ->whereKeyNot($this->getKey())
                ->each(function ($model) {
                    $model->timestamps = false;
                    $model->decrement('order');
                });
        } elseif ($delta < 0) {
            $this->buildSortableQuery()
                ->whereBetween('order', [$this->getPosition(), $this->previousPosition])
                ->whereKeyNot($this->getKey())
                ->each(function ($model) {
                    $model->timestamps = false;
                    $model->increment('order');
                });
        }
    }

    /**
     * Build eloquent builder of sortable.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function buildSortableQuery()
    {
        $query = static::query();

        foreach ($this->getSortableGroupFields() as $field) {
            $query->where($field, $this->$field);
        }

        return $query;
    }

    /**
     * Mutator for position, make sure it always valid.
     *
     * @param int|null $position
     */
    protected function setOrderAttribute(?int $position)
    {
        $lastPosition = $this->getLastPosition();
        $targetPosition = $this->exists ? $lastPosition : $lastPosition + 1;

        if ($position === null) {
            $position = $targetPosition;
        } else {
            $position = max(1, min($position, $lastPosition + 1));
        }

        if ($this->exists) {
            $this->previousPosition = $this->attributes['order'];
        }

        $this->attributes['order'] = $position;
    }

    protected function getSortableField()
    {
        return static::$sortable['column'] ?? 'order';
    }

    protected function getSortableGroupFields(): array
    {
        return (array) (static::$sortable['group_by'] ?? null);
    }
}
