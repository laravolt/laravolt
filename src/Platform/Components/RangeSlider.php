<?php
declare(strict_types=1);
namespace Laravolt\Platform\Components;
use Illuminate\View\Component;

class RangeSlider extends Component
{
    public string $id;
    public string $name;
    public int|float $value;
    public int|float $min;
    public int|float $max;
    public int|float $step;
    public bool $showValue;
    public bool $disabled;

    public function __construct(?string $id = null, ?string $name = null, int|float|null $value = null, int|float|null $min = null, int|float|null $max = null, int|float|null $step = null, ?bool $showValue = null, ?bool $disabled = null)
    {
        $this->id = $id ?? 'range-' . uniqid();
        $this->name = $name ?? $this->id;
        $this->value = $value ?? 50;
        $this->min = $min ?? 0;
        $this->max = $max ?? 100;
        $this->step = $step ?? 1;
        $this->showValue = $showValue ?? true;
        $this->disabled = $disabled ?? false;
    }

    public function render() { return view('laravolt::components.range-slider'); }
}
