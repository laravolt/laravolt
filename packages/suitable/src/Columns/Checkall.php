<?php
namespace Laravolt\Suitable\Columns;

use Illuminate\Support\Facades\View;

class Checkall implements ColumnInterface
{

    protected $headerAttributes = ['width' => '50px'];

    public function header()
    {
        return View::make('suitable::columns.checkall.header')->render();
    }

    public function headerAttributes()
    {
        return $this->headerAttributes;
    }

    public function cell($data)
    {
        return View::make('suitable::columns.checkall.cell', compact('data'))->render();
    }
}
