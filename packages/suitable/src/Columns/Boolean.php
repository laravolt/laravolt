<?php

namespace Laravolt\Suitable\Columns;

class Boolean extends Column implements ColumnInterface
{
    protected $headerAttributes = ['class' => 'center aligned'];

    protected $cellAttributes = ['class' => 'center aligned'];

    public function cell($cell, $collection, $loop)
    {
        return (bool) $cell->{$this->field} ? '<i class="ui empty circular label blue"></i>' : '<i class="ui empty circular label"></i>';
    }
}
