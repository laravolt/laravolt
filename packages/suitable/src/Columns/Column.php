<?php

namespace Laravolt\Suitable\Columns;

abstract class Column
{
    protected $headerAttributes = [];

    protected $cellAttributes = [];

    protected $header = '';

    protected $field;

    public $showOnly = ['html', 'pdf', 'xls', 'xlsx'];

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
        return $this->header;
    }

    public function headerAttributes()
    {
        return $this->headerAttributes;
    }

    public function cellAttributes($cell)
    {
        return $this->cellAttributes;
    }

    public function only($formats)
    {
        $this->showOnly = array_intersect(is_array($formats) ? $formats : func_get_args(), $this->showOnly);

        return $this;
    }
}
