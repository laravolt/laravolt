<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Datepicker extends Component
{
    public string $id;
    public string $name;
    public ?string $value;
    public ?string $placeholder;
    public ?string $format;
    public ?string $min;
    public ?string $max;
    public bool $range;
    public bool $disabled;
    public ?string $size;

    public function __construct(
        ?string $id = null,
        ?string $name = null,
        ?string $value = null,
        ?string $placeholder = null,
        ?string $format = null,
        ?string $min = null,
        ?string $max = null,
        ?bool $range = null,
        ?bool $disabled = null,
        ?string $size = null
    ) {
        $this->id = $id ?? 'datepicker-' . uniqid();
        $this->name = $name ?? $this->id;
        $this->value = $value;
        $this->placeholder = $placeholder ?? 'Select date';
        $this->format = $format ?? 'd/m/Y';
        $this->min = $min;
        $this->max = $max;
        $this->range = $range ?? false;
        $this->disabled = $disabled ?? false;
        $this->size = $size ?? 'md';
    }

    public function render()
    {
        return view('laravolt::components.datepicker');
    }
}
