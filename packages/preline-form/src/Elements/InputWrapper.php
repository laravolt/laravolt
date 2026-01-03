<?php

namespace Laravolt\PrelineForm\Elements;

class InputWrapper extends Text
{
    protected $prependElement;

    protected $appendElement;

    public function prependIcon($icon)
    {
        $this->prependElement = sprintf('<div class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-4"><i class="%s text-gray-400 dark:text-neutral-500"></i></div>', $icon);
        $this->addClass('ps-11');

        return $this;
    }

    public function appendIcon($icon)
    {
        $this->appendElement = sprintf('<div class="absolute inset-y-0 end-0 flex items-center pointer-events-none z-20 pe-4"><i class="%s text-gray-400 dark:text-neutral-500"></i></div>', $icon);
        $this->addClass('pe-11');

        return $this;
    }

    public function prependLabel($label)
    {
        $this->prependElement = sprintf('<span class="inline-flex items-center px-4 min-w-fit rounded-s-md border border-e-0 border-gray-200 bg-gray-50 text-sm text-gray-500 dark:bg-gray-700 dark:border-gray-700 dark:text-gray-400">%s</span>', form_escape($label));
        $this->removeClass('rounded-lg');
        $this->addClass('rounded-e-lg');

        return $this;
    }

    public function appendLabel($label)
    {
        $this->appendElement = sprintf('<span class="inline-flex items-center px-4 min-w-fit rounded-e-md border border-s-0 border-gray-200 bg-gray-50 text-sm text-gray-500 dark:bg-gray-700 dark:border-gray-700 dark:text-gray-400">%s</span>', form_escape($label));
        $this->removeClass('rounded-lg');
        $this->addClass('rounded-s-lg');

        return $this;
    }

    protected function renderControl()
    {
        $output = '';

        if ($this->prependElement || $this->appendElement) {
            $output .= '<div class="relative">';

            if ($this->prependElement) {
                $output .= $this->prependElement;
            }

            $output .= sprintf('<input%s>', $this->renderAttributes());

            if ($this->appendElement) {
                $output .= $this->appendElement;
            }

            $output .= '</div>';
        } else {
            $output = sprintf('<input%s>', $this->renderAttributes());
        }

        return $output;
    }
}
