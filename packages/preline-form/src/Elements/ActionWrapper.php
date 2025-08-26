<?php

namespace Laravolt\PrelineForm\Elements;

class ActionWrapper extends Element
{
    protected $actions;

    public function __construct($actions)
    {
        $this->actions = $actions;
    }

    public function render()
    {
        $result = '<div class="flex gap-2">';

        foreach ($this->actions as $action) {
            $result .= $action->render();
        }

        $result .= '</div>';

        return $result;
    }
}
