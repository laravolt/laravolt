<?php

declare(strict_types=1);

namespace Laravolt\PrelineForm\Elements;

class Time extends Text
{
    public function __construct($name)
    {
        parent::__construct($name);
        $this->setAttribute('type', 'time');
    }
}
