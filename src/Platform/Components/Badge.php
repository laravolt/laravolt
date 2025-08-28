<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Badge extends Component
{
    public $variant = 'default';

    public $size = 'sm';

    public $dot = false;

    public $label = '';

    public function __construct(
        ?string $variant = null,
        ?string $size = null,
        ?bool $dot = null,
        ?string $label = null
    ) {
        $this->variant = $variant ?? $this->variant;
        $this->size = $size ?? $this->size;
        $this->dot = $dot ?? $this->dot;
        $this->label = $label;
    }

    public function render()
    {
        return view('laravolt::components.badge');
    }
}
