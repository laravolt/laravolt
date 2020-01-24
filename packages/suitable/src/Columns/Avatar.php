<?php

namespace Laravolt\Suitable\Columns;

class Avatar extends Column implements ColumnInterface
{
    public function cell($cell, $collection, $loop)
    {
        return "<img class='ui image avatar' src='".\Laravolt\Avatar\Facade::create($cell->{$this->field})->toBase64()."'>";
    }
}
