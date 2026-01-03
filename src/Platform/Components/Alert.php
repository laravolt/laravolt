<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Alert extends Component
{
    public $variant = 'info';

    public $message = '';

    public $title = '';

    public $dismissible = false;

    public $icon = '';

    public $size = 'md';

    public $rounded = true;

    public $border = true;

    public function __construct(
        ?string $variant = null,
        ?string $message = null,
        ?string $title = null,
        ?bool $dismissible = null,
        ?string $icon = null,
        ?string $size = null,
        ?bool $rounded = null,
        ?bool $border = null
    ) {
        $this->variant = $variant ?? $this->variant;
        $this->message = $message;
        $this->title = $title;
        $this->dismissible = $dismissible ?? $this->dismissible;
        $this->icon = $icon;
        $this->size = $size ?? $this->size;
        $this->rounded = $rounded ?? $this->rounded;
        $this->border = $border ?? $this->border;
    }

    public function render()
    {
        return view('laravolt::components.alert');
    }
}
