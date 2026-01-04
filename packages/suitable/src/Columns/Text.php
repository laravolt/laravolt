<?php

declare(strict_types=1);

namespace Laravolt\Suitable\Columns;

class Text extends Column implements ColumnInterface
{
    public function cell($cell, $collection, $loop)
    {
        $data = data_get($cell, $this->field);

        if ($data === null) {
            return '';
        }

        return htmlspecialchars($data, ENT_QUOTES);
    }
}
