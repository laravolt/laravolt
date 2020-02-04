<?php

namespace Laravolt\Suitable\Columns;

use Carbon\Carbon;

class DateTime extends Date implements ColumnInterface
{
    protected $format = 'lll';

    public function cell($cell, $collection, $loop)
    {
        try {
            return Carbon::createFromFormat('Y-m-d H:i:s', $cell->{$this->field})
                ->setTimezone($this->timezone)
                ->isoFormat($this->format);
        } catch (\InvalidArgumentException $e) {
            return $cell->{$this->field};
        }
    }
}
