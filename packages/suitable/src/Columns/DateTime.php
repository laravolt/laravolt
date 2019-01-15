<?php

namespace Laravolt\Suitable\Columns;

class DateTime extends Column implements ColumnInterface
{
    protected $format = 'j F Y H:i:s';

    public function cell($cell, $collection, $loop)
    {
        return \Jenssegers\Date\Date
            ::createFromFormat('Y-m-d H:i:s', $cell->{$this->field})
            ->format($this->format);
    }

    public function format($format)
    {
        $this->format = $format;

        return $this;
    }
}
