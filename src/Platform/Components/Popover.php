<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Popover extends Component
{
    public $placement = 'bottom';

    public $trigger = 'click';

    public $content = '';

    public $title = '';

    public function __construct(
        ?string $placement = null,
        ?string $trigger = null,
        ?string $content = null,
        ?string $title = null
    ) {
        $this->placement = $placement ?? $this->placement;
        $this->trigger = $trigger ?? $this->trigger;
        $this->content = $content;
        $this->title = $title;
    }

    public function render()
    {
        return view('laravolt::components.popover');
    }
}
