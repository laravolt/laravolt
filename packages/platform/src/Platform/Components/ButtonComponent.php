<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class ButtonComponent extends Component
{
    public $label = '';

    public $icon = '';

    public $type = '';

    public $url = '';

    /**
     * PanelComponent constructor.
     *
     * @param string $label
     * @param string $icon
     * @param string $type
     * @param string $url
     */
    public function __construct(string $label, string $icon, string $type, string $url)
    {
        $this->label = $label;
        $this->icon = $icon;
        $this->type = $type;
        $this->url = $url;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('laravolt::components.button');
    }
}
