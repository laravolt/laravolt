<?php

namespace Laravolt\Suitable\Toolbars;

class Search extends Toolbar implements \Laravolt\Suitable\Contracts\Toolbar
{
    protected $name = 'search';

    /**
     * Title constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make($name)
    {
        $toolbar = new static($name);

        return $toolbar;
    }

    public function render()
    {
        return view('suitable::toolbars.search', ['name' => $this->name])->render();
    }
}
