<?php

namespace Laravolt\Platform\Components;

use Illuminate\Support\Stringable;
use Illuminate\View\Component;

class Button extends Component
{
    public $label = '';

    public $icon = '';

    public $class = '';

    public $url = '';

    /**
     * PanelComponent constructor.
     *
     * @param string $label
     * @param string $icon
     * @param string $class
     * @param string $url
     */
    public function __construct(string $label = null, string $class = null, string $url = null, string $icon = null)
    {
        $this->label = $label;
        $this->icon = $icon;
        $this->url = $url;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        $colors = collect(config('laravolt.ui.colors'))->keys();
        $class = (new Stringable($this->class))->explode(' ');

        if ($class->intersect($colors)->isEmpty()) {
            $this->class = config('laravolt.ui.color').' '.$this->class;
        }

        return view('laravolt::components.button');
    }
}
