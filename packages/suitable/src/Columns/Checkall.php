<?php

declare(strict_types=1);

namespace Laravolt\Suitable\Columns;

use Illuminate\Support\Facades\View;

class Checkall extends Column implements ColumnInterface
{
    protected $headerAttributes = ['width' => '50px', 'class' => 'center aligned'];

    protected $cellAttributes = ['class' => 'numbering'];

    private $filldata;

    public static function make($field, $header = null)
    {
        $column = parent::make($field, $header);
        $column->header = View::make('suitable::columns.checkall.header')->render();

        return $column;
    }

    public function header()
    {
        return View::make('suitable::columns.checkall.header')->render();
    }

    public function cell($data, $collection, $loop)
    {
        $checked = $this->isChecked($data);
        $checkboxValue = data_get($data, $this->field);

        return View::make('suitable::columns.checkall.cell', compact('data', 'checked', 'checkboxValue'))->render();
    }

    private function isChecked($data)
    {
        if (! $this->filldata) {
            return false;
        }

        return in_array($data->id, $this->filldata);
    }
}
