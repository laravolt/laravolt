<?php

declare(strict_types=1);

namespace Laravolt\Suitable\Columns;

class View extends Column implements ColumnInterface
{
    protected $view;

    public static function make($view, $header = null)
    {
        $column = new static($header);
        $column->view = $view;

        return $column;
    }

    public function cell($cell, $collection, $loop)
    {
        return \Illuminate\Support\Facades\View::make($this->view, ['data' => $cell])->render();
    }
}
