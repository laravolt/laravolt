<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class ListGroup extends Component
{
    public $variant = 'default';
    public $flush = false;
    public $items = [];

    public function __construct(
        ?string $variant = null,
        ?bool $flush = null,
        ?array $items = null
    ) {
        $this->variant = $variant ?? $this->variant;
        $this->flush = $flush ?? $this->flush;
        $this->items = $items ?? $this->items;
    }

    public function render()
    {
        return view('laravolt::components.list-group');
    }
}
