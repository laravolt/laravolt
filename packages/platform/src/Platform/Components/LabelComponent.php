<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class LabelComponent extends Component
{
    public $label;

    public $color;

    /**
     * PanelComponent constructor.
     *
     * @param string $label
     * @param string $color
     */
    public function __construct(string $label = null, string $color = null)
    {
        $this->label = $label;
        $this->color = $color;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return <<<'blade'
        <div class="ui label {{ $color}}">
            {{ $label ?? $slot }}
        </div>
        blade;
    }
}
