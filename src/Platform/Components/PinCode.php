<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class PinCode extends Component
{
    public $length = 4;
    public $mask = false;
    public $size = 'md';
    public $variant = 'default';
    public $disabled = false;
    public $placeholder = '○';

    public function __construct(
        ?int $length = null,
        ?bool $mask = null,
        ?string $size = null,
        ?string $variant = null,
        ?bool $disabled = null,
        ?string $placeholder = null
    ) {
        $this->length = $length ?? $this->length;
        $this->mask = $mask ?? $this->mask;
        $this->size = $size ?? $this->size;
        $this->variant = $variant ?? $this->variant;
        $this->disabled = $disabled ?? $this->disabled;
        $this->placeholder = $placeholder ?? $this->placeholder;
    }

    public function render()
    {
        return view('laravolt::components.pin-code');
    }
}