<?php

namespace Laravolt\Charts;

use Illuminate\Support\Arr;

abstract class Pie extends Chart
{
    public string $type = self::PIE;

    public function labels(): array
    {
        return collect(Arr::first($this->series))->keys()->toArray();
    }

    protected function formatSeries(): array
    {
        $firstElement = Arr::first($this->series);
        if (is_array($firstElement)) {
            return collect($firstElement)->values()->toArray();
        }

        return collect($this->series)->values()->toArray();
    }
}
