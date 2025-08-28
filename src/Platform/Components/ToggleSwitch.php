<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class ToggleSwitch extends Component
{
    public $label = '';

    public $description = '';

    public $checked = false;

    public $disabled = false;

    public function __construct(
        ?string $label = null,
        ?string $description = null,
        ?bool $checked = null,
        ?bool $disabled = null
    ) {
        $this->label = $label;
        $this->description = $description;
        $this->checked = $checked ?? $this->checked;
        $this->disabled = $disabled ?? $this->disabled;
    }

    public function render()
    {
        return view('laravolt::components.switch');
    }
}
