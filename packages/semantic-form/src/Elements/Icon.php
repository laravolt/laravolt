<?php

namespace Laravolt\SemanticForm\Elements;

class Icon extends Element
{
    protected $attributes = [
        'class' => 'icon',
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
        $html = '<i';
        $html .= $this->renderAttributes();
        $html .= '>';

        $html .= '</i>';

        return $html;
    }
}
