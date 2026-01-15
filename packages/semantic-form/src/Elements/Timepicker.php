<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

use DateTimeImmutable;

class Timepicker extends Text
{
    protected $attributes = [
        'type' => 'text',
        'readonly' => 'readonly',
    ];

    public function value($value)
    {
        if ($value instanceof DateTimeImmutable) {
            $value = $value->format('H:i');
        }

        return parent::value($value);
    }
}
