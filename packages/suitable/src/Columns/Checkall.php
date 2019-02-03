<?php
namespace Laravolt\Suitable\Columns;

use Illuminate\Support\Facades\View;

class Checkall implements ColumnInterface
{
    private $filldata;

    protected $headerAttributes = ['width' => '50px'];

    public function __construct($filldata = array())
    {
        $this->filldata = $filldata;
    }

    private function isChecked($data){
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

    public function cellAttributes($cell)
    {
        return null;
    }

    public function sortable()
    {
        return null;
    }
}
