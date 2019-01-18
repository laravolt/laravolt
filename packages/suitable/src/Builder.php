<?php

namespace Laravolt\Suitable;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\View;

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
        $this->columns = $columns;

        return $this;
    }

    public function getColumns()
    {
        return $this->columns;
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
        $page = $this->collection->currentPage();
        $total = max(1, ceil($this->collection->total() / $this->collection->perPage()));

        return trans('suitable::pagination.pager', compact('page', 'total'));
    }

    public function sequence($item)
    {
        $collections = collect($this->collection->items());
        $index = $collections->search($item) + 1;
        $start = (request('page', 1) - 1) * $this->collection->perPage();

        return $start + $index;
    }
}
