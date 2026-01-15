<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Steps extends Component
{
    public $steps = [];

    public $currentStep = 1;

    public $orientation = 'horizontal';

    public $size = 'md';

    public $variant = 'default';

    public $showNumbers = true;

    public $clickable = false;

    public function __construct(
        ?array $steps = null,
        ?int $currentStep = null,
        ?string $orientation = null,
        ?string $size = null,
        ?string $variant = null,
        ?bool $showNumbers = null,
        ?bool $clickable = null
    ) {
        $this->steps = $steps ?? $this->steps;
        $this->currentStep = $currentStep ?? $this->currentStep;
        $this->orientation = $orientation ?? $this->orientation;
        $this->size = $size ?? $this->size;
        $this->variant = $variant ?? $this->variant;
        $this->showNumbers = $showNumbers ?? $this->showNumbers;
        $this->clickable = $clickable ?? $this->clickable;
    }

    public function render()
    {
        return view('laravolt::components.steps');
    }
}
