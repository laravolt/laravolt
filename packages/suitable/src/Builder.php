<?php

namespace Laravolt\Suitable;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Laravolt\Suitable\Columns\Raw;
use Laravolt\Suitable\Columns\Text;

class Builder
{
    protected $collection = null;

    protected $id = null;

    protected $columns = [];

    protected $baseRoute = null;

    protected $showPagination = false;

    protected $row;

    protected $format;

    protected $segments = [];

    protected $view = 'suitable::container';

    /**
     * Builder constructor.
     */
    public function __construct()
    {
        $this->id = 'suitable'.str_random();
    }

    public function source($collection)
    {
        $this->collection = $collection;

        if ($collection instanceof LengthAwarePaginator) {
            $this->showPagination = true;
        }

        return $this;
    }

    public function id($id)
    {
        $this->id = $id;

        return $this;
    }

    public function segments(array $segments)
    {
        $this->segments = $segments;

        return $this;
    }

    public function addSegment($segment)
    {
        $this->segments[] = $segment;
    }

    public function getDefaultSegment()
    {
        return array_first($this->segments);
    }

    public function columns(array $columns)
    {
        $this->columns = collect($columns)->transform(function ($column) {
            if (is_array($column)) {
                $column = $this->transformColumn($column);
            }

            return $column;
        });

        return $this;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function filterColumns($filters)
    {
        $filters = is_array($filters) ? $filters : func_get_args();
        $this->columns = collect($this->columns)->filter(function($item) use ($filters) {
            return in_array($item->id(), $filters);
        });
    }

    public function baseRoute($route)
    {
        $this->baseRoute = $route;

        return $this;
    }

    public function row($view)
    {
        if (view()->exists($view)) {
            $this->row = $view;
        }

        return $this;
    }

    public function render($view = null)
    {
        $view = $view ?: $this->view;

        $data = [
            'collection'     => $this->collection,
            'id'             => $this->id,
            'columns'        => $this->columns,
            'showPagination' => $this->showPagination,
            'row'            => $this->row,
            'format'         => $this->format,
            'segments'       => $this->segments,
            'builder'        => $this,
        ];

        return View::make($view, $data)->render();
    }

    public function summary()
    {
        if (!$this->collection instanceof LengthAwarePaginator) {
            return false;
        }

        $from = (($this->collection->currentPage() - 1) * $this->collection->perPage()) + 1;
        $total = $this->collection->total();

        if ($this->collection->hasMorePages()) {
            $to = $from + $this->collection->perPage() - 1;
        } else {
            $to = $total;
        }

        if ($total > 0) {
            return trans('suitable::pagination.summary', compact('from', 'to', 'total'));
        }

        return trans('suitable::pagination.empty');
    }

    public function pager()
    {
        if (!$this->collection instanceof LengthAwarePaginator) {
            return $this->total();
        }

        $page = $this->collection->currentPage();
        $total = max(1, ceil($this->collection->total() / $this->collection->perPage()));

        return trans('suitable::pagination.pager', compact('page', 'total'));
    }

    public function total()
    {
        $count = false;
        if ($this->collection instanceof Collection) {
            $count = count($this->collection);
        } elseif ($this->collection instanceof LengthAwarePaginator) {
            $count = $this->collection->total();
        }

        if ($count !== false) {
            return trans('suitable::pagination.total', compact('count'));
        }

        return false;
    }

    public function sequence($item)
    {
        $collections = collect($this->collection->items());
        $index = $collections->search($item) + 1;
        $start = (request('page', 1) - 1) * $this->collection->perPage();

        return $start + $index;
    }

    protected function transformColumn($column)
    {
        $header = array_get($column, 'header');

        if (array_has($column, 'raw') && $column['raw'] instanceof \Closure) {
            return Raw::make($column['raw'], $header);
        }

        if ($view = array_get($column, 'view')) {
            return \Laravolt\Suitable\Columns\View::make($view, $header);
        }

        if ($field = array_get($column, 'field')) {
            return Text::make($field, $header);
        }

        return false;
    }

}
