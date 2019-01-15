<?php

namespace Laravolt\Suitable\Columns;

class Image extends Column implements ColumnInterface
{
    public function cell($cell, $collection, $loop)
    {
        return sprintf('<img style="height:50px;" class="ui image" src="%s" />', $cell->{$this->field});
    }
}
