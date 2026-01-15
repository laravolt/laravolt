<?php

declare(strict_types=1);

namespace Laravolt\Suitable\Columns;

class Boolean extends Column implements ColumnInterface
{
    protected $headerAttributes = ['class' => 'center aligned'];

    protected $cellAttributes = ['class' => 'center aligned'];

    public function cell($cell, $collection, $loop)
    {
        $color = config('laravolt.ui.color');

        return (bool) data_get($cell, $this->field) ? "<i class='ui empty circular label $color'></i>" : '<i class="ui empty circular label"></i>';
    }
}
