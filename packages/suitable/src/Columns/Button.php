<?php

namespace Laravolt\Suitable\Columns;

class Button extends Column implements ColumnInterface
{
    public function cell($cell, $collection, $loop)
    {
        $url = data_get($cell, $this->field);

        return sprintf('<a class="ui basic button icon %s" themed href="%s"><i class="icon eye"></i></a>', config('laravolt.ui.color'), url($url));
    }
}
