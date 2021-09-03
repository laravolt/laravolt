<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Icon extends Component
{
    public string $name;

    /**
     * PanelComponent constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
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
                ->width('16px')
                ->toHtml();
        };
    }
}
