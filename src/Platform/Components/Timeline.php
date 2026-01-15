<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Timeline extends Component
{
    public $items = [];

    public $orientation = 'vertical';

    public $size = 'md';

    public $variant = 'default';

    public $showConnector = true;

    public $showIcons = true;

    public function __construct(
        ?array $items = null,
        ?string $orientation = null,
        ?string $size = null,
        ?string $variant = null,
        ?bool $showConnector = null,
        ?bool $showIcons = null
    ) {
        $this->items = $items ?? $this->items;
        $this->orientation = $orientation ?? $this->orientation;
        $this->size = $size ?? $this->size;
        $this->variant = $variant ?? $this->variant;
        $this->showConnector = $showConnector ?? $this->showConnector;
        $this->showIcons = $showIcons ?? $this->showIcons;
    }

    public function render()
    {
        return view('laravolt::components.timeline');
    }
}
