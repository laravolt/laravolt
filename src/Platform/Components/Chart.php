<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Chart extends Component
{
    public string $id;
    public string $type;
    public array $series;
    public array $categories;
    public ?int $height;
    public ?string $title;
    public array $colors;
    public bool $stacked;
    public bool $toolbar;

    public function __construct(
        ?string $id = null,
        ?string $type = null,
        ?array $series = null,
        ?array $categories = null,
        ?int $height = null,
        ?string $title = null,
        ?array $colors = null,
        ?bool $stacked = null,
        ?bool $toolbar = null
    ) {
        $this->id = $id ?? 'chart-' . uniqid();
        $this->type = $type ?? 'bar';
        $this->series = $series ?? [];
        $this->categories = $categories ?? [];
        $this->height = $height ?? 320;
        $this->title = $title;
        $this->colors = $colors ?? ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];
        $this->stacked = $stacked ?? false;
        $this->toolbar = $toolbar ?? true;
    }

    public function render()
    {
        return view('laravolt::components.chart');
    }
}
