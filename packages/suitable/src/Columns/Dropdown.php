<?php
namespace Laravolt\Suitable\Columns;

use Illuminate\Support\Facades\View;

class Dropdown implements ColumnInterface
{
    public function header()
    {
        return '';
    }

    public function cell($cell)
    {
        return View::make('suitable::dropdown.cell')->render();
    }


}
