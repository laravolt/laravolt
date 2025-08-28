<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class FileInput extends Component
{
    public $label = '';
    public $helper = '';
    public $error = '';
    public $multiple = false;
    public $accept = '';
    public $maxSize = '';
    public $dragDrop = true;

    public function __construct(
        ?string $label = null,
        ?string $helper = null,
        ?string $error = null,
        ?bool $multiple = null,
        ?string $accept = null,
        ?string $maxSize = null,
        ?bool $dragDrop = null
    ) {
        $this->label = $label;
        $this->helper = $helper;
        $this->error = $error;
        $this->multiple = $multiple ?? $this->multiple;
        $this->accept = $accept;
        $this->maxSize = $maxSize;
        $this->dragDrop = $dragDrop ?? $this->dragDrop;
    }

    public function render()
    {
        return view('laravolt::components.file-input');
    }
}
