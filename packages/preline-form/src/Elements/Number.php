<?php

declare(strict_types=1);

namespace Laravolt\PrelineForm\Elements;

class Number extends Text
{
    public function __construct($name)
    {
        parent::__construct($name);
        $this->setAttribute('type', 'number');
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

    public function step($step)
    {
        $this->setAttribute('step', $step);

        return $this;
    }
}
