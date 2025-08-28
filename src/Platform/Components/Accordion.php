<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Accordion extends Component
{
    public $items = [];
    public $allowMultiple = false;
    public $size = 'md';
    public $variant = 'default';
    public $bordered = true;
    public $flush = false;

    public function __construct(
        ?array $items = null,
        ?bool $allowMultiple = null,
        ?string $size = null,
        ?string $variant = null,
        ?bool $bordered = null,
        ?bool $flush = null
    ) {
        $this->items = $items ?? $this->items;
        $this->allowMultiple = $allowMultiple ?? $this->allowMultiple;
        $this->size = $size ?? $this->size;
        $this->variant = $variant ?? $this->variant;
        $this->bordered = $bordered ?? $this->bordered;
        $this->flush = $flush ?? $this->flush;
    }

    public function render()
    {
        return view('laravolt::components.accordion');
    }
}