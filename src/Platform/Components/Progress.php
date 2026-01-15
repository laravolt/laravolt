<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Progress extends Component
{
    public $value = 0;

    public $max = 100;

    public $size = 'md';

    public $variant = 'primary';

    public $showLabel = false;

    public $label = '';

    public function __construct(
        ?int $value = null,
        ?int $max = null,
        ?string $size = null,
        ?string $variant = null,
        ?bool $showLabel = null,
        ?string $label = null
    ) {
        $this->value = $value ?? $this->value;
        $this->max = $max ?? $this->max;
        $this->size = $size ?? $this->size;
        $this->variant = $variant ?? $this->variant;
        $this->showLabel = $showLabel ?? $this->showLabel;
        $this->label = $label;
    }

    public function render()
    {
        return view('laravolt::components.progress');
    }
}
