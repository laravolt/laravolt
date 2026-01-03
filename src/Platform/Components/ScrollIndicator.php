<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class ScrollIndicator extends Component
{
    public $variant = 'top';

    public $size = 'md';

    public $color = 'blue';

    public $position = 'fixed';

    public $target = 'body';

    public function __construct(
        ?string $variant = null,
        ?string $size = null,
        ?string $color = null,
        ?string $position = null,
        ?string $target = null
    ) {
        $this->variant = $variant ?? $this->variant;
        $this->size = $size ?? $this->size;
        $this->color = $color ?? $this->color;
        $this->position = $position ?? $this->position;
        $this->target = $target ?? $this->target;
    }

    public function render()
    {
        return view('laravolt::components.scroll-indicator');
    }
}
