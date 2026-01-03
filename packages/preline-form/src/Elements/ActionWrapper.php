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
        $elements = '';

        foreach ($this->actions as $action) {
            $elements .= $action->render();
        }

        return <<<HTML
          <div class="p-4 pt-0 flex justify-end gap-x-2">
            <div class="w-full flex justify-end items-center gap-x-2">
              $elements
            </div>
          </div>
        HTML;
    }
}
