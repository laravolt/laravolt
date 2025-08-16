<?php

namespace Laravolt\PrelineForm\Elements;

class Date extends Text
{
    public function __construct($name)
    {
        parent::__construct($name);
        $this->setAttribute('type', 'date');
    }
}