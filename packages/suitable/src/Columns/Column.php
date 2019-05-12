<?php

namespace Laravolt\Suitable\Columns;

use Laravolt\Suitable\Concerns\HtmlHelper;
use Laravolt\Suitable\Headers\Header;
use Laravolt\Suitable\Headers\SortableHeader;

abstract class Column
{
    use HtmlHelper;

    protected $headerAttributes = [];

    protected $cellAttributes = [];

    protected $header = '';

    protected $id;

    protected $field;

    protected $sortableColumn = '';

    protected $searchableColumn = '';

    protected function __construct($header)
    {
        $this->header = $header;
    }

    static public function make($field, $header = null)
    {
        if ($header === null) {
            $header = str_replace('_', ' ', title_case($field));
        }

        $column = new static($header);
        $column->id = snake_case($header);

        if (is_string($field) || $field instanceof \Closure) {
            $column->field = $field;
        }

        return $column;
    }

    public function id()
    {
        return $this->id;
    }

    public function header()
    {
        if ($this->isSortable()) {
            $header = SortableHeader::make($this->header, $this->sortableColumn);
        } else {
            $header = Header::make($this->header);
        }

        $header->setAttributes($this->headerAttributes());

        return $header;
    }

    public function headerAttributes()
    {
        return $this->headerAttributes;
    }

    public function cellAttributes($cell)
    {
        return $this->tagAttributes((array) $this->cellAttributes);
    }

    public function sortable($column = null)
    {
        $this->sortableColumn = $column ?: $this->field;

        return $this;
    }

    protected function isSortable()
    {
        return (bool) $this->sortableColumn;
    }

    public function searchable($column = null)
    {
        $this->searchableColumn = $column ?: $this->field;

        return $this;
    }

    protected function isSearchable()
    {
        return (bool) $this->searchableColumn;
    }
}
