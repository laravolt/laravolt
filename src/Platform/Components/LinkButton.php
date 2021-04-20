<?php

namespace Laravolt\Platform\Components;

use Illuminate\Support\Stringable;
use Illuminate\View\Component;

class LinkButton extends Component
{
    public $label;

    public $icon;

    public $url;

    public $class;

    /**
     * PanelComponent constructor.
     *
     * @param string $label
     * @param string $icon
     * @param string $url
     * @param string $class
     */
    public function __construct(string $url, string $label = null, string $icon = null, string $class = 'secondary')
    {
        $this->url = $url;
        $this->label = $label;
        $this->icon = $icon;
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
        $classes = (new Stringable($this->class))->explode(' ');

        if ($classes->intersect($colors)->isEmpty()) {
            $this->class = config('laravolt.ui.color').' '.$this->class;
        }

        return view('laravolt::components.link-button');
    }
}
