<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Notification extends Component
{
    public $title = '';

    public $message = '';

    public $variant = 'info';

    public $position = 'top-right';

    public $dismissible = true;

    public $autoHide = false;

    public $duration = 5000;

    public $icon = '';

    public $actions = [];

    public function __construct(
        ?string $title = null,
        ?string $message = null,
        ?string $variant = null,
        ?string $position = null,
        ?bool $dismissible = null,
        ?bool $autoHide = null,
        ?int $duration = null,
        ?string $icon = null,
        ?array $actions = null
    ) {
        $this->title = $title ?? $this->title;
        $this->message = $message ?? $this->message;
        $this->variant = $variant ?? $this->variant;
        $this->position = $position ?? $this->position;
        $this->dismissible = $dismissible ?? $this->dismissible;
        $this->autoHide = $autoHide ?? $this->autoHide;
        $this->duration = $duration ?? $this->duration;
        $this->icon = $icon ?? $this->icon;
        $this->actions = $actions ?? $this->actions;
    }

    public function render()
    {
        return view('laravolt::components.notification');
    }
}
