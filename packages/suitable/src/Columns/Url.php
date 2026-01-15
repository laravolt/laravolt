<?php

declare(strict_types=1);

namespace Laravolt\Suitable\Columns;

class Url extends Column implements ColumnInterface
{
    public function cell($cell, $collection, $loop)
    {
        $url = data_get($cell, $this->field);

        return sprintf('<a themed href="%s">%s</a>', url($url), $url);
    }
}
