<?php
namespace Laravolt\Suitable;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\View;
use Laravolt\Suitable\Columns\ColumnInterface;

class Builder
{

    protected $collection = null;

    protected $id = null;

    protected $headers = [];

    protected $fields = [];

    protected $title = null;

    protected $toolbars = [];

    protected $baseRoute = null;

    protected $showSearch = true;

    protected $showPagination = false;

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

    public function columns(array $columns)
    {
        foreach ($columns as $column) {
            $this->headers[] = $this->getHeader($column);
            $this->fields[] = $column;
        }

        return $this;
    }

    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    public function search($showSearch)
    {
        $this->showSearch = $showSearch;

        return $this;
    }

    public function addToolbar($html)
    {
        $this->toolbars[] = $html;

        return $this;
    }

    public function baseRoute($route)
    {
        $this->baseRoute = $route;

        return $this;
    }

    public function render()
    {
        $data = [
            'collection'     => $this->collection,
            'id'             => $this->id,
            'headers'        => $this->headers,
            'fields'         => $this->fields,
            'title'          => $this->title,
            'showSearch'     => $this->showSearch,
            'showPagination' => $this->showPagination,
            'toolbars'       => $this->toolbars,
            'builder'        => $this
        ];

        return View::make('suitable::table', $data)->render();
    }

    public function renderCell($field, $data)
    {
        if (array_has($field, 'raw') && $field['raw'] instanceof \Closure) {
            return call_user_func($field['raw'], $data);
        }

        if ($view = array_get($field, 'view')) {
            return View::make($view, compact('data'))->render();
        }

        if (array_has($field, 'field')) {
            return $data[$field['field']];
        }

        if (array_has($field, 'present')) {
            return $data->present($field['present']);
        }

        if (array_has($field, 'view')) {
            return render($field['view'], compact('data'));
        }

        if ($field instanceof ColumnInterface) {
            return $field->cell($data);
        }

        return false;
    }

    public function renderCellAttributes($field, $data)
    {
        $html = '';
        if ($attributes = array_get($field, 'cellAttributes')) {
            foreach ($attributes as $attribute => $value) {
                $html .= " {$attribute}=\"{$value}\"";
            }
        }

        return $html;
    }

    protected function getHeader($column)
    {
        $header = new Header();
        $headerAttributes = array_get($column, 'headerAttributes');

        $sortable = array_get($column, 'sortable', false);
        if ($sortable) {
            unset($column['sortable']);
            $html = Sortable::link([array_get($column, 'field', ''), array_get($column, 'header', '')]);
        } elseif (is_array($column)) {
            $html = array_get($column, 'header', '');
        } elseif ($column instanceof ColumnInterface) {
            $html = $column->header();
            $headerAttributes = $column->headerAttributes();
        } else {
            throw new \Exception('Invalid header value');
        }

        $header->setSortable($sortable);
        $header->setHtml($html);

        $header->setAttributes($headerAttributes);

        return $header;
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
