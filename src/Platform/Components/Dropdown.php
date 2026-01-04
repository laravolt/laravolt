<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Dropdown extends Component
{
    public $placement = 'bottom-left';

    public $trigger = 'click';

    public $offset = '0';

    public $header = '';

    public $menu = '';

    public $footer = '';

    public function __construct(
        ?string $placement = null,
        ?string $trigger = null,
        ?string $offset = null,
        ?string $header = null,
        ?string $menu = null,
        ?string $footer = null
    ) {
        $this->placement = $placement ?? $this->placement;
        $this->trigger = $trigger ?? $this->trigger;
        $this->offset = $offset ?? $this->offset;
        $this->header = $header;
        $this->menu = $menu;
        $this->footer = $footer;
    }

    public function render()
    {
        return view('laravolt::components.dropdown');
    }
}
