<?php

declare(strict_types=1);

namespace Laravolt\Workflow\FieldFormatter;

use Illuminate\Support\Arr;
use Laravolt\Workflow\FieldFormatter\Db\DbFormatterFactory;

class DbFormatter
{
    public static function format($data, $fields)
    {
        $formattedData = [];
        foreach ($fields as $field) {
            $formattedData[$field['field_name']] = DbFormatterFactory::make(
                $field['field_name'],
                Arr::get($data, $field['field_name']),
                $field['field_type']
            );
        }

        return $formattedData;
    }
}
