<?php

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Carousel extends Component
{
    public $items = [];
    public $autoplay = false;
    public $interval = 3000;
    public $indicators = true;
    public $controls = true;
    public $fade = false;
    public $loop = true;

    public function __construct(
        ?array $items = null,
        ?bool $autoplay = null,
        ?int $interval = null,
        ?bool $indicators = null,
        ?bool $controls = null,
        ?bool $fade = null,
        ?bool $loop = null
    ) {
        $this->items = $items ?? $this->items;
        $this->autoplay = $autoplay ?? $this->autoplay;
        $this->interval = $interval ?? $this->interval;
        $this->indicators = $indicators ?? $this->indicators;
        $this->controls = $controls ?? $this->controls;
        $this->fade = $fade ?? $this->fade;
        $this->loop = $loop ?? $this->loop;
    }

    public function render()
    {
        return view('laravolt::components.carousel');
    }
}