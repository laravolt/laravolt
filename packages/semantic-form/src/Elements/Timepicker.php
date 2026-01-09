<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

use DateTimeInterface;

class Timepicker extends Text
{
    protected $attributes = [
        'type' => 'text',
        'readonly' => 'readonly',
    ];

    public function value($value)
    {
        if ($value instanceof DateTimeInterface) {
            $value = $value->format('H:i');
        }

        return parent::value($value);
    }
}
