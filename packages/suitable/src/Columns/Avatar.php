<?php

declare(strict_types=1);

namespace Laravolt\Suitable\Columns;

class Avatar extends Column implements ColumnInterface
{
    public function cell($cell, $collection, $loop)
    {
        return "<img class='inline-block size-11 rounded-full' src='".\Laravolt\Avatar\Facade::create($cell->{$this->field})->toBase64()."'>";
    }
}
