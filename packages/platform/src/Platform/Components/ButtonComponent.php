<?php

namespace Laravolt\Platform\Components;

use Illuminate\Support\Stringable;
use Illuminate\View\Component;

class ButtonComponent extends Component
{
    public $label = '';

    public $icon = '';

    public $type = 'primary';

    public $url = '';

    /**
     * PanelComponent constructor.
     *
     * @param string $label
     * @param string $icon
     * @param string $type
     * @param string $url
     */
    public function __construct(string $label = null, string $type = null, string $url = null, string $icon = null)
    {
        $this->label = $label;
        $this->icon = $icon;
        if ($type) {
            $this->type = $type;
        }
        $this->url = $url;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        $class = $this->type;
        $colors = collect(config('laravolt.ui.colors'))->keys();
        $types = (new Stringable($this->type))->explode(' ');

        if ($types->intersect($colors)->isEmpty()) {
            $class = config('laravolt.ui.color').' '.$this->type;
        }

        return view('laravolt::components.button', compact('class'));
    }
}
