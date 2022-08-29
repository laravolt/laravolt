<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Icon extends Component
{
    public string $name;

    public int $size;

    /**
     * PanelComponent constructor.
     *
     * @param string $name
     */
    public function __construct(string $name, int $size = 16)
    {
        $this->name = $name;
        $this->size = $size;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Closure
     */
    public function render()
    {
        return function (array $data) {
            $iconset = config('laravolt.ui.iconset');

            return svg("$iconset-{$this->name}", null, $data['attributes']->merge(['class' => 'x-icon'])->getAttributes())
                ->width($this->size.'px')
                ->toHtml();
        };
    }
}
