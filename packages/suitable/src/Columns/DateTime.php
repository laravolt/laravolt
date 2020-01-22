<?php

namespace Laravolt\Suitable\Columns;

class DateTime extends Column implements ColumnInterface
{
    protected $format = 'j F Y H:i:s';

    public function cell($cell, $collection, $loop)
    {
        try {
            return \Jenssegers\Date\Date
                ::createFromFormat('Y-m-d H:i:s', $cell->{$this->field})
                ->format($this->format);
        } catch (\InvalidArgumentException $e) {
            return $cell->{$this->field};
        }
    }

    public function format($format)
    {
        $this->format = $format;

        return $this;
    }
}
