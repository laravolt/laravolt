<?php

namespace Laravolt\Platform\Components;

use Illuminate\Support\Stringable;
use Illuminate\View\Component;

class Button extends Component
{
    public $label = '';
    public $icon = '';
    public $class = '';
    public $url = '';
    public $variant = 'primary';
    public $size = 'md';
    public $iconPosition = 'left';
    public $loading = false;
    public $disabled = false;
    public $pill = false;

    /**
     * Button constructor with enhanced Preline UI v3.0 support.
     */
    public function __construct(
        ?string $label = null,
        ?string $class = null,
        ?string $url = null,
        ?string $icon = null,
        ?string $variant = null,
        ?string $size = null,
        ?string $iconPosition = null,
        ?bool $loading = null,
        ?bool $disabled = null,
        ?bool $pill = null
    ) {
        $this->label = $label;
        $this->icon = $icon;
        $this->url = $url;
        $this->class = $class;
        $this->variant = $variant ?? $this->variant;
        $this->size = $size ?? $this->size;
        $this->iconPosition = $iconPosition ?? $this->iconPosition;
        $this->loading = $loading ?? $this->loading;
        $this->disabled = $disabled ?? $this->disabled;
        $this->pill = $pill ?? $this->pill;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        $colors = collect(config('laravolt.ui.colors'))->keys();
        $class = (new Stringable($this->class))->explode(' ');

        if ($class->intersect($colors)->isEmpty()) {
            $this->class = config('laravolt.ui.color').' '.$this->class;
        }

        return view('laravolt::components.button');
    }
}
