<?php

namespace Laravolt\Suitable\Toolbars;

abstract class Toolbar
{
    protected $class = [];

    public function __toString()
    {
        return $this->render();
    }

    public function addClass($class)
    {
        $this->class[] = $class;

        return $this;
    }

    abstract public function render();
}
