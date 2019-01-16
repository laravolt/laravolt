<?php

namespace Laravolt\Suitable\Columns;

abstract class Column
{
    protected $headerAttributes = [];

    protected $cellAttributes = [];

    protected $header = '';

    protected $field;

    protected $sortableColumn = '';

    public $showOnly = ['html', 'print', 'pdf', 'xls', 'xlsx'];

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

    public function only($formats)
    {
        $this->showOnly = array_intersect(is_array($formats) ? $formats : func_get_args(), $this->showOnly);

        return $this;
    }

    public function except($formats)
    {
        $this->showOnly = array_diff($this->showOnly, is_array($formats) ? $formats : func_get_args());

        return $this;
    }

    public function showOn($format)
    {
        return in_array($format, $this->showOnly);
    }

    public function hideOn($format)
    {
        return !$this->showOn($format);
    }

    public function sortable($column = null)
    {
        $this->sortableColumn = $column;

        return $this;
    }
}
