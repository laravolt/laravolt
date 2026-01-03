<?php

namespace Laravolt\Platform\Components;

class DataDisplay extends \Illuminate\View\Component
{
    public function __construct(
        public string $label,
        public string $value
    ) {
        $this->label = $label;
        $this->value = $value;
    }

    public function render()
    {
        return view('laravolt::components.data-display', [
            'label' => $this->label,
            'value' => $this->value,
        ]);
    }
}
