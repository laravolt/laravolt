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

    public function __construct(
        ?string $variant = null,
        ?string $message = null,
        ?string $title = null,
        ?bool $dismissible = null,
        ?string $icon = null
    ) {
        $this->variant = $variant ?? $this->variant;
        $this->message = $message;
        $this->title = $title;
        $this->dismissible = $dismissible ?? $this->dismissible;
        $this->icon = $icon;
    }

    public function render()
    {
        return view('laravolt::components.alert');
    }
}
