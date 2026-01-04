<?php

declare(strict_types=1);

namespace Laravolt\Suitable\Columns;

class Number extends Column implements ColumnInterface
{
    protected $headerAttributes = ['class' => 'right aligned'];

    protected $cellAttributes = ['class' => 'right aligned'];

    public function cell($cell, $collection, $loop)
    {
        return number_format((int) data_get($cell, $this->field), 0, ',', '.');
    }
}
