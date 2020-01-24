<?php

namespace Laravolt\Suitable\Toolbars;

class Text extends Toolbar implements \Laravolt\Suitable\Contracts\Toolbar
{
    protected $tag = 'h4';

    protected $label = '';

    /**
     * Title constructor.
     *
     * @param string $label
     */
    public function __construct(string $label)
    {
        $this->label = $label;
    }

    public static function make($label)
    {
        $toolbar = new static($label);

        return $toolbar;
    }

    public function render()
    {
        return sprintf('<%1$s>%2$s</%1$s>', $this->tag, $this->label);
    }
}
