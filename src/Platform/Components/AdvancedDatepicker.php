<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class AdvancedDatepicker extends Component
{
    public $label = '';

    public $placeholder = 'Select date';

    public $range = false;

    public $disabled = false;

    public $error = '';

    public $size = 'md';

    public $clearable = true;

    public $minDate = null;

    public $maxDate = null;

    public $format = 'Y-m-d';

    public function __construct(
        ?string $label = null,
        ?string $placeholder = null,
        ?bool $range = null,
        ?bool $disabled = null,
        ?string $error = null,
        ?string $size = null,
        ?bool $clearable = null,
        ?string $minDate = null,
        ?string $maxDate = null,
        ?string $format = null
    ) {
        $this->label = $label ?? $this->label;
        $this->placeholder = $placeholder ?? $this->placeholder;
        $this->range = $range ?? $this->range;
        $this->disabled = $disabled ?? $this->disabled;
        $this->error = $error;
        $this->size = $size ?? $this->size;
        $this->clearable = $clearable ?? $this->clearable;
        $this->minDate = $minDate;
        $this->maxDate = $maxDate;
        $this->format = $format ?? $this->format;
    }

    public function render()
    {
        return view('laravolt::components.advanced-datepicker');
    }
}
