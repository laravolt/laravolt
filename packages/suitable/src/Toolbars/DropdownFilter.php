<?php

namespace Laravolt\Suitable\Toolbars;

use Illuminate\Support\Str;

class DropdownFilter extends Toolbar implements \Laravolt\Suitable\Contracts\Toolbar
{
    protected $label;

    protected $name = 'filter';

    protected $options = [];

    /**
     * Title constructor.
     *
     * @param string $name
     * @param array  $options
     */
    public function __construct(string $name, array $options)
    {
        $this->name = $name;
        $this->options = $options;
    }

    public static function make(string $name, array $options)
    {
        $toolbar = new static($name, $options);

        return $toolbar;
    }

    public function label(string $label)
    {
        $this->label = $label;

        return $this;
    }

    public function render()
    {
        $label = $this->label ?? Str::title($this->name);

        return view('suitable::toolbars.dropdown',
            ['label' => $label, 'name' => $this->name, 'options' => $this->options])->render();
    }
}
