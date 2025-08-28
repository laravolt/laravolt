<?php

namespace Laravolt\PrelineForm\Elements;

class Label extends Element
{
    protected $label;

    public function __construct($label)
    {
        $this->label = $label;
        $this->addClass('block text-sm font-medium mb-2 dark:text-white');
    }

    public function render()
    {
        return sprintf('<label%s>%s</label>', $this->renderAttributes(), form_escape($this->label));
    }
}
