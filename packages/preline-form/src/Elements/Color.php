<?php

namespace Laravolt\PrelineForm\Elements;

class Color extends Text
{
    public function __construct($name)
    {
        parent::__construct($name);
        $this->setAttribute('type', 'color');
    }
}