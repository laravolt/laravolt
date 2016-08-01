<?php
namespace Laravolt\Suitable\Columns;

use Illuminate\Support\Facades\View;

class Checkall implements ColumnInterface
{
    public function header()
    {
        return View::make('suitable::checkall.header')->render();
    }

    public function cell($data)
    {
        return View::make('suitable::checkall.cell', compact('data'))->render();
    }
}
