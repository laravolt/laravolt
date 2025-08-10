<?php

namespace Laravolt\SemanticForm\Elements;

class UiLabel extends Element
{
    protected $attributes = [
        'class' => 'inline-flex items-center px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded dark:bg-neutral-700 dark:text-neutral-300',
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
