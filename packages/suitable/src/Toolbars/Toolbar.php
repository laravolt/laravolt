<?php

declare(strict_types=1);

namespace Laravolt\Suitable\Toolbars;

abstract class Toolbar
{
    protected $class = [];

    public function __toString()
    {
        return $this->render();
    }

    abstract public function render();

    public function addClass($class)
    {
        $this->class[] = $class;

        return $this;
    }
}
