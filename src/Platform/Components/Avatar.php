<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Avatar extends Component
{
    public $src = '';

    public $alt = '';

    public $initials = '';

    public $size = 'md';

    public $status = null;

    public $badge = null;

    public function __construct(
        ?string $src = null,
        ?string $alt = null,
        ?string $initials = null,
        ?string $size = null,
        ?string $status = null,
        ?string $badge = null
    ) {
        $this->src = $src;
        $this->alt = $alt;
        $this->initials = $initials;
        $this->size = $size ?? $this->size;
        $this->status = $status;
        $this->badge = $badge;
    }

    public function render()
    {
        return view('laravolt::components.avatar');
    }
}
