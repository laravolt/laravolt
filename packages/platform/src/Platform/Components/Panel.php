<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Panel extends Component
{
    public $title = '';

    public $icon = '';

    public $iconClass = '';

    /**
     * PanelComponent constructor.
     *
     * @param string $title
     * @param string $icon
     * @param string $iconClass
     */
    public function __construct(string $title = '', string $icon = '', string $iconClass = '')
    {
        $this->title = $title;
        $this->icon = $icon;
        $this->iconClass = $iconClass;
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
