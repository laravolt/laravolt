<?php

namespace Laravolt\Charts;

abstract class Donut extends Chart
{
    public string $type = self::DONUT;

    protected function labels(): array
    {
        return collect($this->series())->keys()->toArray();
    }

    protected function formatSeries(): array
    {
        return collect($this->series)->values()->toArray();
    }
}
