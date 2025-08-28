<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Tooltip extends Component
{
    public $placement = 'top';
    public $content = '';
    public $trigger = 'hover';

    public function __construct(
        ?string $placement = null,
        ?string $content = null,
        ?string $trigger = null
    ) {
        $this->placement = $placement ?? $this->placement;
        $this->content = $content;
        $this->trigger = $trigger ?? $this->trigger;
    }

    public function render()
    {
        return view('laravolt::components.tooltip');
    }
}
