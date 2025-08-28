<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Table extends Component
{
    public $headers = [];

    public $rows = [];

    public $striped = false;

    public $bordered = true;

    public $hover = true;

    public $responsive = true;

    public $size = 'md';

    public function __construct(
        ?array $headers = null,
        ?array $rows = null,
        ?bool $striped = null,
        ?bool $bordered = null,
        ?bool $hover = null,
        ?bool $responsive = null,
        ?string $size = null
    ) {
        $this->headers = $headers ?? $this->headers;
        $this->rows = $rows ?? $this->rows;
        $this->striped = $striped ?? $this->striped;
        $this->bordered = $bordered ?? $this->bordered;
        $this->hover = $hover ?? $this->hover;
        $this->responsive = $responsive ?? $this->responsive;
        $this->size = $size ?? $this->size;
    }

    public function render()
    {
        return view('laravolt::components.table');
    }
}
