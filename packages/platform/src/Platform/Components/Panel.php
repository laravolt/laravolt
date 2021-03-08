<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Panel extends Component
{
    public $title = '';

    public $icon = '';

    /**
     * PanelComponent constructor.
     *
     * @param string $title
     * @param string $icon
     */
    public function __construct(string $title = '', string $icon = '')
    {
        $this->title = $title;
        $this->icon = $icon;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('laravolt::components.panel');
    }
}
