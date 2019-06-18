<?php

namespace Laravolt\Suitable\Columns;

class Label extends Column implements ColumnInterface
{
    protected $headerAttributes = ['class' => 'center aligned'];

    protected $cellAttributes = ['class' => 'center aligned'];

    public function cell($cell, $collection, $loop)
    {
        $label = $cell->{$this->field};
        if ($label) {
            return sprintf('<div class="ui label basic">%s</div>', $cell->{$this->field});
        }

        return '-';
    }
}
