<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Titlebar extends Component
{
    public $title = '';

    /**
     * PanelComponent constructor.
     */
    public function __construct(string $title)
    {
        $this->title = $title;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('laravolt::components.titlebar');
    }
}
