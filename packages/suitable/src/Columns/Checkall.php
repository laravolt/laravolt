<?php

namespace Laravolt\Suitable\Columns;

use Illuminate\Support\Facades\View;

class Checkall extends Column implements ColumnInterface
{
    private $filldata;

    protected $headerAttributes = ['width' => '50px'];

    protected $cellAttributes = ['class' => 'numbering'];

    private function isChecked($data)
    {
        if (!$this->filldata) {
            return false;
        }

        return in_array($data->id, $this->filldata);
    }

    public function header()
    {
        return View::make('suitable::columns.checkall.header')->render();
    }

    public function headerAttributes()
    {
        return $this->headerAttributes;
    }

    public function cell($data, $collection, $loop)
    {
        $checked = $this->isChecked($data);

        return View::make('suitable::columns.checkall.cell', compact('data', 'checked'))->render();
    }
}
