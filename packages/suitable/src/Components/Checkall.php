<?php
namespace Laravolt\Suitable\Components;

use Laravolt\Suitable\Components\Component as BaseComponent;
use Laravolt\Suitable\Contracts\Component;

class Checkall extends BaseComponent implements Component
{
    public function header()
    {
        return render('suitable::checkall.header');
    }

    public function cell($data)
    {
        return render('suitable::checkall.cell', compact('data'));
    }
}
