<?php

namespace Laravolt\PrelineForm\Elements;

class Label extends Element
{
    protected $label;

    public function __construct($label)
    {
        $this->label = $label;
        $this->addClass('sm:mt-2.5 inline-block text-sm text-gray-500 dark:text-neutral-500');
    }

    public function render()
    {
        return sprintf('<label%s>%s</label>', $this->renderAttributes(), form_escape($this->label));
    }
}
