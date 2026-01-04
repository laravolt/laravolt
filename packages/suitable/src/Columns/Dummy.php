<?php

declare(strict_types=1);

namespace Laravolt\Suitable\Columns;

class Dummy extends Column implements ColumnInterface
{
    public function cell($cell, $collection, $loop)
    {
        return '';
    }
}
