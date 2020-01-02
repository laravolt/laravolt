<?php

namespace Laravolt\Suitable\Columns;

class Raw extends Column implements ColumnInterface
{
    public function cell($cell, $collection, $loop)
    {
        return call_user_func($this->field, $cell, $collection, $loop);
    }
}
