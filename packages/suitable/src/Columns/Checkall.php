<?php
namespace Laravolt\Suitable\Columns;

use Illuminate\Support\Facades\View;

class Checkall implements ColumnInterface
{
    public function header()
    {
        return View::make('suitable::columns.checkall.header')->render();
    }

    public function cell($data)
    {
        return View::make('suitable::columns.checkall.cell', compact('data'))->render();
    }
}
