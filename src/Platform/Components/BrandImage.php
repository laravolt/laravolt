<?php

namespace Laravolt\Platform\Components;

use Illuminate\Support\Str;
use Illuminate\View\Component;

class BrandImage extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        $brandImage = config('laravolt.ui.brand_image');
        $isSvg = Str::of($brandImage)->startsWith('<svg');

        if (!$brandImage) {
            $brandImage = 'img/app.png';
        }
        return view('laravolt::components.brand-image', compact('brandImage', 'isSvg'));
    }
}
