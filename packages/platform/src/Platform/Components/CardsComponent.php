<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class CardsComponent extends Component
{
    public function render()
    {
        return <<<'blade'
        <div class="ui cards">
            {{ $slot }}
        </div>
        blade;
    }
}
