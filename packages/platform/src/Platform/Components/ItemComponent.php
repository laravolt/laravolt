<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class ItemComponent extends Component
{
    public function render()
    {
        return <<<'blade'
        <div {{ $attributes->merge(['class' => 'item'])}} >
            {{ $slot }}
        </div>
        blade;
    }
}
