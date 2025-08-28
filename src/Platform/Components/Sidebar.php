<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Sidebar extends Component
{
    public $variant = 'default';
    public $collapsible = false;

    public function __construct(
        ?string $variant = null,
        ?bool $collapsible = null
    ) {
        $this->variant = $variant ?? $this->variant;
        $this->collapsible = $collapsible ?? $this->collapsible;
    }

    public function render()
    {
        return view('laravolt::components.sidebar');
    }
}
