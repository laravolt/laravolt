<?php

namespace Laravolt\Suitable;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Laravolt\Suitable\Columns\ColumnInterface;
use Laravolt\Suitable\Columns\Dummy;
use Laravolt\Suitable\Columns\Raw;
use Laravolt\Suitable\Segments\Segment;
use Laravolt\Suitable\Toolbars\Search;
use Laravolt\Suitable\Toolbars\Text;

class Builder
{
    protected $collection = null;

    protected $id = null;

    protected $title;

    protected $columns = [];

    protected $searchableColumns = [];

    protected $baseRoute = null;

    protected $showPagination = false;

    protected $paginationView = 'suitable::pagination.simple';

    protected $search;

    protected $row;

    protected $format;

    protected $segments = [];

    protected $view = 'suitable::container';

    /**
     * Builder constructor.
     */
    public function __construct()
    {
        // Generate default value
        $this->id = 'suitable'.Str::random();

        // Add default segment
        $segment = Segment::make('default');
        $this->addSegment($segment);
    }

    public function source($collection)
    {
        if ($collection instanceof \Closure) {
            $collection = call_user_func($collection);
        }

        $this->collection = $collection;

        if ($collection instanceof Paginator) {
            $this->showPagination = true;

            if ($collection instanceof LengthAwarePaginator) {
                $this->paginationView = 'suitable::pagination.full';
            }
        }

        return $this;
    }

    public function id($id)
    {
        $this->id = $id;

        return $this;
    }

    public function title(string $title)
    {
        $this->getDefaultSegment()->left(Text::make($title));

        return $this;
    }

    public function search($search = true)
    {
        if (!is_bool($search) && !is_string($search)) {
            throw new \InvalidArgumentException('Only boolean or string allowed');
        }
        if ($search === true) {
            $this->search = config('suitable.query_string.search');
        } else {
            $this->search = $search;
        }

        return $this;
    }

    public function segments(array $segments)
    {
        $this->segments = $segments;

        return $this;
    }

    public function addSegment(Segment $segment)
    {
        $this->segments[$segment->getKey()] = $segment;
    }

    public function getDefaultSegment()
    {
        return $this->segments['default'] ?? null;
    }

    public function columns(array $columns)
    {
        $this->columns = collect($columns)->transform(function ($column) {
            if (is_string($column)) {
                $column = ['header' => $column, 'field' => $column];
            }

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
        $this->columns = collect($this->columns)->filter(function ($item) use ($filters) {
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

        if ($this->search) {
            $this->getDefaultSegment()->right(Search::make($this->search));
        }

        $data = [
            'collection' => $this->collection,
            'id' => $this->id,
            'columns' => $this->columns,
            'hasSearchableColumns' => optional(optional($this->columns)->first)->isSearchable() !== null,
            'showPagination' => $this->showPagination,
            'showHeader' => collect($this->segments)->first->isNotEmpty(),
            'showFooter' => $this->showPagination && !$this->collection->isEmpty(),
            'paginationView' => $this->paginationView,
            'row' => $this->row,
            'format' => $this->format,
            'segments' => $this->segments,
            'builder' => $this,
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
        $header = Arr::get($column, 'header');

        $columnObject = Dummy::make(null, $header);

        if (Arr::has($column, 'raw') && $column['raw'] instanceof \Closure) {
            $columnObject = Raw::make($column['raw'], $header);
        }

        if ($view = Arr::get($column, 'view')) {
            $columnObject = \Laravolt\Suitable\Columns\View::make($view, $header);
        }

        if ($field = Arr::get($column, 'field')) {
            $columnObject = \Laravolt\Suitable\Columns\Text::make($field, $header);
        }

        if ($columnObject instanceof ColumnInterface) {
            if (Arr::has($column, 'sortable')) {
                $columnObject->sortable($column['sortable']);
            }

            if (Arr::has($column, 'searchable')) {
                $columnObject->searchable($column['searchable']);
            }

            if (Arr::has($column, 'headerAttributes')) {
                $columnObject->setHeaderAttributes($column['headerAttributes']);
            }

            if (Arr::has($column, 'cellAttributes')) {
                $columnObject->setCellAttributes($column['cellAttributes']);
            }
        }

        return $columnObject;
    }
}
