<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class TimePicker extends Component
{
    public $label = '';

    public $placeholder = 'Select time';

    public $format = '24h';

    public $disabled = false;

    public $error = '';

    public $size = 'md';

    public $clearable = true;

    public function __construct(
        ?string $label = null,
        ?string $placeholder = null,
        ?string $format = null,
        ?bool $disabled = null,
        ?string $error = null,
        ?string $size = null,
        ?bool $clearable = null
    ) {
        $this->label = $label ?? $this->label;
        $this->placeholder = $placeholder ?? $this->placeholder;
        $this->format = $format ?? $this->format;
        $this->disabled = $disabled ?? $this->disabled;
        $this->error = $error;
        $this->size = $size ?? $this->size;
        $this->clearable = $clearable ?? $this->clearable;
    }

    public function render()
    {
        return view('laravolt::components.time-picker');
    }
}
