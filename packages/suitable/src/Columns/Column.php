<?php

namespace Laravolt\Suitable\Columns;

use Illuminate\Support\Str;
use Laravolt\Suitable\Concerns\HtmlHelper;
use Laravolt\Suitable\Headers\Header;
use Laravolt\Suitable\Headers\Search\TextHeader;
use Laravolt\Suitable\Headers\SortableHeader;

abstract class Column
{
    use HtmlHelper;

    protected $headerAttributes = [];

    protected $cellAttributes = [];

    protected $header = '';

    protected $searchableHeader;

    protected $id;

    protected $field;

    protected $sortableColumn = '';

    protected $searchableColumn = '';

    protected function __construct($header)
    {
        $this->header = $header;
    }

    public static function make($field, $header = null)
    {
        if ($header === null && is_string($field)) {
            $header = str_replace('_', ' ', Str::title($field));
        }

        $column = new static($header);
        $column->id = Str::snake($header);

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

    public function searchableHeader()
    {
        return $this->searchableHeader;
    }

    public function headerAttributes()
    {
        return $this->headerAttributes;
    }

    public function setHeaderAttributes(array $attributes)
    {
        $this->headerAttributes = $attributes;

        return $this;
    }

    public function cellAttributes($cell)
    {
        return $this->tagAttributes((array) $this->cellAttributes);
    }

    public function setCellAttributes(array $attributes)
    {
        $this->cellAttributes = $attributes;

        return $this;
    }

    public function sortable($column = null)
    {
        $this->sortableColumn = ($column === null || $column === true) ? $this->field : $column;

        return $this;
    }

    public function isSortable()
    {
        return (bool) $this->sortableColumn;
    }

    public function searchable($column = null, ?\Laravolt\Suitable\Contracts\Header $header = null)
    {
        $this->searchableColumn = ($column === null || $column === true) ? $this->field : $column;

        $this->searchableHeader = $header ?? TextHeader::make($this->searchableColumn);

        return $this;
    }

    public function setSearchable($column = null)
    {
        $this->searchable($column);

        return $this;
    }

    public function isSearchable()
    {
        return (bool) $this->searchableColumn;
    }
}
