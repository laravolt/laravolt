<?php

namespace Laravolt\Suitable\Columns;

use Illuminate\Contracts\Pagination\Paginator;

class Numbering extends Column implements ColumnInterface
{
    protected $headerAttributes = ['class' => 'numbering center aligned'];

    protected $cellAttributes = ['class' => 'numbering'];

    public static function make($field, $header = null)
    {
        return parent::make($field, $field);
    }

    public function cell($cell, $collection, $loop)
    {
        if ($collection instanceof Paginator) {
            return (($collection->currentPage() - 1) * $collection->perPage()) + $loop->iteration;
        }

        return $loop->iteration;
    }
}
