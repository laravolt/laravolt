<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Stepper extends Component
{
    public $steps = [];
    public $currentStep = 1;
    public $orientation = 'horizontal';
    public $variant = 'default';

    public function __construct(
        ?array $steps = null,
        ?int $currentStep = null,
        ?string $orientation = null,
        ?string $variant = null
    ) {
        $this->steps = $steps ?? $this->steps;
        $this->currentStep = $currentStep ?? $this->currentStep;
        $this->orientation = $orientation ?? $this->orientation;
        $this->variant = $variant ?? $this->variant;
    }

    public function render()
    {
        return view('laravolt::components.stepper');
    }
}
