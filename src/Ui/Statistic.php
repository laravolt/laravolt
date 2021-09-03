<?php

namespace Laravolt\Ui;

use Livewire\Component;

class Statistic extends Component
{
    public int|string $value = '';

    public string $title = '';

    public string $label = '';

    public ?string $icon = null;

    public ?string $color = null;

    public function value(): int|string
    {
        return $this->value;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function icon(): ?string
    {
        return $this->icon;
    }

    public function color(): ?string
    {
        return $this->color ?? config('laravolt.ui.color');
    }

    public function render()
    {
        return view('laravolt::ui-component.statistic');
    }
}
