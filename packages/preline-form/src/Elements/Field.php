<?php

declare(strict_types=1);

namespace Laravolt\PrelineForm\Elements;

class Field extends Element
{
    protected $content;

    public function __construct($content = null)
    {
        $this->content = $content;
        $this->addClass('space-y-1');
    }

    public function render()
    {
        $output = sprintf('<div%s>', $this->renderAttributes());

        if ($this->content) {
            $output .= $this->content;
        }

        $output .= '</div>';

        return $output;
    }
}
