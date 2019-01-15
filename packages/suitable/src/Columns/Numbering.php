<?php

namespace Laravolt\Suitable\Columns;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Numbering extends Column implements ColumnInterface
{
    protected $headerAttributes = ['class' => 'numbering center aligned'];

    protected $cellAttributes = ['class' => 'numbering'];

    public function cell($cell, $collection, $loop)
    {
        if ($collection instanceof LengthAwarePaginator) {
            return (($collection->currentPage() - 1) * $collection->perPage()) + $loop->iteration;
        }

        return $loop->iteration;
    }
}
