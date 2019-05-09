<?php

namespace Laravolt\Suitable\Columns;

use Laravolt\Suitable\Headers\SortableHeader;

abstract class Column
{
    protected $headerAttributes = [];

    protected $cellAttributes = [];

    protected $header = '';

    protected $id;

    protected $field;

    protected $sortableColumn = '';

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
            return SortableHeader::make($this->header, $this->sortableColumn);
        }

        return sprintf('<th %s>%s</th>', $this->generateAttributes($this->headerAttributes), $this->header);
    }

    public function headerAttributes()
    {
        return $this->headerAttributes;
    }

    public function cellAttributes($cell)
    {
        return $this->generateAttributes((array) $this->cellAttributes);
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

    protected function generateAttributes(array $attributes)
    {
        $html = '';

        foreach ($attributes as $attribute => $value) {
            $html .= " {$attribute}=\"{$value}\"";
        }

        return $html;
    }
}
