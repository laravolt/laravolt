<?php

namespace Laravolt\SemanticForm\Elements;

class Datepicker extends Date
{
    protected $attributes = [
        'type' => 'text',
        // 'readonly' => 'readonly',
    ];

    public function value($value)
    {
        if ($value instanceof \DateTime) {
            $value = $value->format('Y-m-d');
        }

        return parent::value($value);
    }
}
