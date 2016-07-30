<?php
namespace Laravolt\Suitable\Components;

use Illuminate\Support\Facades\View;
use Laravolt\Suitable\Components\Component as BaseComponent;
use Laravolt\Suitable\Contracts\Component;

class Checkall extends BaseComponent implements Component
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
