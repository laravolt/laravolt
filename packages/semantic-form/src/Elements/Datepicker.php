<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

use DateTimeInterface;

class Datepicker extends Date
{
    protected $format = 'Y-m-d';

    protected $attributes = [
        'type' => 'text',
    ];

    public function setFormat(string $format)
    {
        $this->format = $format;

        return $this;
    }

    public function value($value)
    {
        if ($value instanceof DateTimeInterface) {
            $value = $value->format($this->format);
        }

        return parent::value($value);
    }
}
