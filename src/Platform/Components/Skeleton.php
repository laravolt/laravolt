<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Skeleton extends Component
{
    public $variant = 'text';
    public $lines = 1;
    public $width = null;
    public $height = null;
    public $rounded = true;
    public $animate = true;

    public function __construct(
        ?string $variant = null,
        ?int $lines = null,
        ?string $width = null,
        ?string $height = null,
        ?bool $rounded = null,
        ?bool $animate = null
    ) {
        $this->variant = $variant ?? $this->variant;
        $this->lines = $lines ?? $this->lines;
        $this->width = $width;
        $this->height = $height;
        $this->rounded = $rounded ?? $this->rounded;
        $this->animate = $animate ?? $this->animate;
    }

    public function render()
    {
        return view('laravolt::components.skeleton');
    }
}
