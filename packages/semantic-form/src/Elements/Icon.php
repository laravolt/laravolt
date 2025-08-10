<?php

namespace Laravolt\SemanticForm\Elements;

class Icon extends Element
{
    protected $attributes = [
        'class' => 'inline-flex items-center text-gray-400',
    ];

    /**
     * Icon constructor.
     *
     * @param string icon name
     */
    public function __construct($icon)
    {
        $this->addClass($icon);
    }

    public function render()
    {
        $html = '<span';
        $html .= $this->renderAttributes();
        $html .= '>';

        $html .= '</span>';

        return $html;
    }
}
