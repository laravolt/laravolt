<?php

namespace Laravolt\Suitable\Columns;

class Text extends Column implements ColumnInterface
{
    public function cell($cell, $collection, $loop)
    {
        return htmlspecialchars($cell->{$this->field});
    }
}
