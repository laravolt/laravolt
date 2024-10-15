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
