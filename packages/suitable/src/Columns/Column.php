<?php

namespace Laravolt\Suitable\Columns;

use Laravolt\Suitable\Headers\SortableHeader;

abstract class Column
{
    protected $headerAttributes = [];

    protected $cellAttributes = [];

    protected $header = '';

    protected $field;

    protected $sortableColumn = '';

    public function __construct($header)
    {
        $this->header = $header;
    }

    static public function make($header, $field = null)
    {
        $column = new static($header);

        if ($field) {
            if (is_string($field)) {
                $column->field = $field;
            } elseif ($field instanceof \Closure) {
                $column->field = $field;
            }
        } else {
            $column->field = snake_case($header);
        }

        return $column;
    }

    public function header()
    {
        if ($this->isSortable()) {
            return SortableHeader::make($this->header, $this->sortableColumn);
        }

        return sprintf('<th>%s</th>', $this->header);
    }

    public function headerAttributes()
    {
        return $this->headerAttributes;
    }

    public function cellAttributes($cell)
    {
        $html = '';

        foreach ((array)$this->cellAttributes as $attribute => $value) {
            $html .= " {$attribute}=\"{$value}\"";
        }

        return $html;
    }

    public function sortable($column = null)
    {
        $this->sortableColumn = $column ?: $this->field;

        return $this;
    }

    protected function isSortable()
    {
        return (bool)$this->sortableColumn;
    }
}
