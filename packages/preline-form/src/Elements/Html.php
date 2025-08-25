<?php

namespace Laravolt\PrelineForm\Elements;

class Html extends Element
{
    protected $content;

    public function __construct($content = '')
    {
        $this->content = $content;
    }

    public function render()
    {
        if ($this->label) {
            return $this->renderField();
        }

        return $this->content;
    }

    protected function renderControl()
    {
        return $this->content;
    }
}