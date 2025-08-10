<?php

namespace Laravolt\SemanticForm\Elements;

class UiLabel extends Element
{
    protected $attributes = [
        'class' => 'inline-flex items-center rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs text-gray-700 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300',
    ];

    protected $text;

    /**
     * Icon constructor.
     *
     * @param string icon name
     */
    public function __construct($text)
    {
        $this->text = $text;
    }

    public function render()
    {
        $html = '<div';
        $html .= $this->renderAttributes();
        $html .= '>';

        $html .= $this->text;

        $html .= '</div>';

        return $html;
    }
}
