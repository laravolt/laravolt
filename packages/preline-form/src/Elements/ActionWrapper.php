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
        $result = '<div class="p-6 pt-0 flex justify-end gap-x-2">';
        $result = '<div class="w-full flex justify-end items-center gap-x-2">';

        foreach ($this->actions as $action) {
            $result .= $action->render();
        }

        $result .= '</div>';
        $result .= '</div>';

        return $result;
    }
}
