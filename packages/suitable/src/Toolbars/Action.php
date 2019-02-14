<?php

namespace Laravolt\Suitable\Toolbars;

class Action extends Toolbar implements \Laravolt\Suitable\Contracts\Toolbar
{
    protected $label = '';

    protected $href = '';

    /**
     * Title constructor.
     * @param string $label
     * @param string $href
     */
    public function __construct(string $label, string $href)
    {
        $this->label = $label;
        $this->href = $href;
    }

    static public function make($label, $href)
    {
        $toolbar = new static($label, $href);

        return $toolbar;
    }

    public function render()
    {
        return sprintf('<a href="%s" class="ui button">%s</a>', $this->href, $this->label);
    }
}
