<?php

namespace Laravolt\Suitable\Columns;

class Id extends Column implements ColumnInterface
{
    public static function make($field = null, $header = null)
    {
        return parent::make($field, $header);
    }

    public function cell($cell, $collection, $loop)
    {
        return $cell->getKey();
    }
}
