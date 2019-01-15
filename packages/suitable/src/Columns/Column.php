<?php

namespace Laravolt\Suitable\Columns;

abstract class Column
{
    protected $headerAttributes = [];

    protected $cellAttributes = [];

    protected $header = '';

    protected $field;

    public function __construct($header)
    {
        $this->header = $header;
    }

    static public function make($header, $field = null)
    {
        $column = new static($header);
        $column->field = $field ?? snake_case($header);

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
}
