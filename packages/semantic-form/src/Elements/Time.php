<?php

namespace Laravolt\SemanticForm\Elements;

class Time extends Text
{
    protected $attributes = [
        'type' => 'time',
    ];

    public function value($value)
    {
        if ($value instanceof \DateTime) {
            $value = $value->format('H:i:s');
        }

        return parent::value($value);
    }

    public function step($value)
    {
        $this->attribute('step', $value);

        return $this;
    }
}
