<?php

declare(strict_types=1);

namespace Laravolt\PrelineForm\Elements;

class FieldsOpen extends Element
{
    public function __construct()
    {
        $this->addClass('space-y-4');
    }

    public function render()
    {
        return sprintf('<div%s>', $this->renderAttributes());
    }
}
