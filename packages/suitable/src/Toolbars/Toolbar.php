<?php

namespace Laravolt\Suitable\Toolbars;

abstract class Toolbar
{
    public function __toString()
    {
        return $this->render();
    }

    abstract function render();
}
