
<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Modal extends Component
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('laravolt::components.modal');
    }
}
