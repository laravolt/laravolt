<?php

namespace Laravolt\UiComponent\Livewire\Base;

abstract class Donut extends Chart
{
    protected function labels(): array
    {
        return collect($this->series())->keys()->toArray();
    }

    protected function formatSeries(): array
    {
        return collect($this->series)->values()->toArray();
    }
}
