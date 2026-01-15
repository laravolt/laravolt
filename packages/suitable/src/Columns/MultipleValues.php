<?php

declare(strict_types=1);

namespace Laravolt\Suitable\Columns;

class MultipleValues extends Column implements ColumnInterface
{
    protected $headerAttributes = ['class' => ''];

    protected $cellAttributes = ['class' => ''];

    public function cell($cell, $collection, $loop)
    {
        $color = config('laravolt.ui.color');

        return collect(data_get($cell, $this->field, []))
            ->transform(fn ($item) => "<div class='ui label $color'>$item</div>")
            ->implode(' ');
    }
}
