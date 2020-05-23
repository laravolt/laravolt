<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class CardComponent extends Component
{
    public function render()
    {
        return view('laravolt::components.card');
    }
}
