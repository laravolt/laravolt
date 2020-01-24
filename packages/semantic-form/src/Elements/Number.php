<?php

namespace Laravolt\SemanticForm\Elements;

class Number extends Text
{
    protected $attributes = [
        'type' => 'number',
    ];

    public function step($step)
    {
        $this->setAttribute('step', $step);

        return $this;
    }

    public function min($min)
    {
        $this->setAttribute('min', $min);

        return $this;
    }

    public function max($max)
    {
        $this->setAttribute('max', $max);

        return $this;
    }
}
