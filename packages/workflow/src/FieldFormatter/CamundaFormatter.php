<?php

declare(strict_types=1);

namespace Laravolt\Workflow\FieldFormatter;

use Illuminate\Support\Arr;
use Laravolt\Workflow\FieldFormatter\Camunda\CamundaFormatterFactory;

class CamundaFormatter
{
    public static function format($data, $schema)
    {
        $formattedData = [];
        foreach ($schema as $name => $field) {
            $formattedData[$name] = CamundaFormatterFactory::make(
                $name,
                Arr::get($data, $name),
                $field['type']
            );
        }

        return $formattedData;
    }
}
