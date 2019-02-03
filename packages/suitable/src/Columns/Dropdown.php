<?php
namespace Laravolt\Suitable\Columns;

use Illuminate\Support\Facades\View;
use Lavary\Menu\Menu;

class Dropdown implements ColumnInterface
{

    protected $text;

    protected $menus;

    protected $direction = 'right';

    /**
     * Dropdown constructor.
     */
    public function __construct($text, \Closure $closure)
    {
        $this->menus = (new Menu)->make($this->getId(), $closure);
        $this->setText($text);
    }

    public function header()
    {
        return '';
    }

    public function headerAttributes()
    {
        return null;
    }

    public function cell($cell, $collection, $loop)
    {
        $data['text'] = $this->text;
        $data['menus'] = $this->menus->roots();
        $data['direction'] = $this->direction;

        return View::make('suitable::columns.dropdown.cell', $data)->render();
    }

    public function cellAttributes($cell)
    {
        return null;
    }

    public function setDirection($direction)
    {
        $this->direction = $direction;

        return $this;
    }

    protected function getId()
    {
        return 'suitable-dropdown-'.str_random();
    }

    protected function setText($text)
    {
        if ($text) {
            $this->text = $text;
        }
    }

    public function sortable()
    {
        return null;
    }
}
