<?php

namespace Laravolt\Suitable\Columns;

class Date extends Column implements ColumnInterface
{
    protected $format = 'j F Y';

    public function cell($cell, $collection, $loop)
    {
        $field = $cell->{$this->field};

        try {
            return \Jenssegers\Date\Date::createFromFormat('Y-m-d', $field)->format($this->format);
        } catch (\InvalidArgumentException $e) {
            try {
                return \Jenssegers\Date\Date::createFromFormat('Y-m-d H:i:s', $field)->format($this->format);
            } catch (\InvalidArgumentException $e) {
                return $field;
            }
        }
    }

    public function format($format)
    {
        $this->format = $format;

        return $this;
    }
}
