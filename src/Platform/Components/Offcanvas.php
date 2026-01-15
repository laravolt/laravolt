<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Offcanvas extends Component
{
    public $placement = 'end';

    public $show = false;

    public $backdrop = true;

    public $scrollable = false;

    public $title = '';

    public $content = '';

    public $footer = '';

    public function __construct(
        ?string $placement = null,
        ?bool $show = null,
        ?bool $backdrop = null,
        ?bool $scrollable = null,
        ?string $title = null,
        ?string $content = null,
        ?string $footer = null
    ) {
        $this->placement = $placement ?? $this->placement;
        $this->show = $show ?? $this->show;
        $this->backdrop = $backdrop ?? $this->backdrop;
        $this->scrollable = $scrollable ?? $this->scrollable;
        $this->title = $title;
        $this->content = $content;
        $this->footer = $footer;
    }

    public function render()
    {
        return view('laravolt::components.offcanvas');
    }
}
