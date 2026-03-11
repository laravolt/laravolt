<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class InputNumber extends Component
{
    public string $id;
    public string $name;
    public int|float $value;
    public int|float|null $min;
    public int|float|null $max;
    public int|float $step;
    public ?string $size;
    public bool $disabled;
    public ?string $prefix;
    public ?string $suffix;

    public function __construct(
        ?string $id = null,
        ?string $name = null,
        int|float|null $value = null,
        int|float|null $min = null,
        int|float|null $max = null,
        int|float|null $step = null,
        ?string $size = null,
        ?bool $disabled = null,
        ?string $prefix = null,
        ?string $suffix = null
    ) {
        $this->id = $id ?? 'input-number-' . uniqid();
        $this->name = $name ?? $this->id;
        $this->value = $value ?? 0;
        $this->min = $min;
        $this->max = $max;
        $this->step = $step ?? 1;
        $this->size = $size ?? 'md';
        $this->disabled = $disabled ?? false;
        $this->prefix = $prefix;
        $this->suffix = $suffix;
    }

    public function render()
    {
        return view('laravolt::components.input-number');
    }
}
