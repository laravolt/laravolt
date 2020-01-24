<?php

namespace Laravolt\SemanticForm\Elements;

class Timepicker extends Text
{
    protected $attributes = [
        'type' => 'text',
        'readonly' => 'readonly',
    ];

    public function value($value)
    {
        if ($value instanceof \DateTime) {
            $value = $value->format('H:i');
        }

        return parent::value($value);
    }
}
