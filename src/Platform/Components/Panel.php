<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Panel extends Component
{
    public string $title;

    public string $icon;

    public string $iconClass;

    public string $description;

    /**
     * PanelComponent constructor.
     *
     * @param string $title
     * @param string $description
     * @param string $icon
     * @param string $iconClass
     */
    public function __construct(string $title = '', string $description = '', string $icon = '', string $iconClass = '')
    {
        $this->title = $title;
        $this->icon = $icon;
        $this->iconClass = $iconClass;
        $this->description = $description;
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
