<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Radio extends Component
{
    public $label = '';
    public $description = '';
    public $checked = false;
    public $disabled = false;
    public $value = '';

    public function __construct(
        ?string $label = null,
        ?string $description = null,
        ?bool $checked = null,
        ?bool $disabled = null,
        ?string $value = null
    ) {
        $this->label = $label;
        $this->description = $description;
        $this->checked = $checked ?? $this->checked;
        $this->disabled = $disabled ?? $this->disabled;
        $this->value = $value;
    }

    public function render()
    {
        return view('laravolt::components.radio');
    }
}
