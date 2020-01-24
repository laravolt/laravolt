<?php

namespace Laravolt\SemanticForm\Elements;

class FieldsOpen extends Element
{
    protected $attributes = [
        'class' => 'fields',
    ];

    public function render()
    {
        $html = '<div';
        $html .= $this->renderAttributes();
        $html .= '>';

        return $html;
    }
}
