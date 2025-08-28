<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Toast extends Component
{
    public $variant = 'info';

    public $position = 'bottom-right';

    public $autoHide = true;

    public $delay = 5000;

    public $show = true;

    public $message = '';

    public $title = '';

    public function __construct(
        ?string $variant = null,
        ?string $position = null,
        ?bool $autoHide = null,
        ?int $delay = null,
        ?bool $show = null,
        ?string $message = null,
        ?string $title = null
    ) {
        $this->variant = $variant ?? $this->variant;
        $this->position = $position ?? $this->position;
        $this->autoHide = $autoHide ?? $this->autoHide;
        $this->delay = $delay ?? $this->delay;
        $this->show = $show ?? $this->show;
        $this->message = $message;
        $this->title = $title;
    }

    public function render()
    {
        return view('laravolt::components.toast');
    }
}
