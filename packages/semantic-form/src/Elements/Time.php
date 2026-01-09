<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

use DateTimeInterface;

class Time extends Text
{
    protected $attributes = [
        'type' => 'time',
    ];

    public function value($value)
    {
        if ($value instanceof DateTimeInterface) {
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
