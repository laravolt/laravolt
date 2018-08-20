<?php

namespace Laravolt\Suitable\Columns;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Numbering implements ColumnInterface
{
    protected $headerAttributes = ['class' => 'numbering center aligned'];
    protected $cellAttributes = ['class' => 'numbering'];
    protected $header = '';

    /**
     * Numbering constructor.
     */
    public function __construct($header)
    {
        $this->header = $header;
    }

    public function header()
    {
        return $this->header;
    }

    public function headerAttributes()
    {
        return $this->headerAttributes;
    }

    public function cellAttributes($cell)
    {
        return $this->cellAttributes;
    }

    public function cell($cell, $collection, $loop)
    {
        if ($collection instanceof LengthAwarePaginator) {
            return (($collection->currentPage() - 1 ) * $collection->perPage() ) + $loop->iteration;
        }

        return $loop->iteration;
    }

}
