<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Rating extends Component
{
    public $value = 0;

    public $max = 5;

    public $size = 'md';

    public $variant = 'yellow';

    public $readonly = false;

    public $showCount = false;

    public $count = null;

    public $precision = 1;

    public function __construct(
        ?float $value = null,
        ?int $max = null,
        ?string $size = null,
        ?string $variant = null,
        ?bool $readonly = null,
        ?bool $showCount = null,
        ?int $count = null,
        ?float $precision = null
    ) {
        $this->value = $value ?? $this->value;
        $this->max = $max ?? $this->max;
        $this->size = $size ?? $this->size;
        $this->variant = $variant ?? $this->variant;
        $this->readonly = $readonly ?? $this->readonly;
        $this->showCount = $showCount ?? $this->showCount;
        $this->count = $count;
        $this->precision = $precision ?? $this->precision;
    }

    public function render()
    {
        return view('laravolt::components.rating');
    }
}
