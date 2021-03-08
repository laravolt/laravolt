<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Backlink extends Component
{
    public $label = '';

    public $url = '';

    /**
     * PanelComponent constructor.
     *
     * @param string $label
     * @param string $url
     */
    public function __construct(string $url, string $label = null)
    {
        $this->label = $label;
        $this->url = $url;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('laravolt::components.backlink');
    }
}
