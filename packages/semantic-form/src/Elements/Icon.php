<?php namespace Laravolt\SemanticForm\Elements;

class Icon extends Element
{
    protected $attributes = [
        'class' => 'icon',
    ];

    public function render()
    {

        $html = '<i';
        $html .= $this->renderAttributes();
        $html .= '>';

        $html .= '</i>';

        return $html;
    }

}
