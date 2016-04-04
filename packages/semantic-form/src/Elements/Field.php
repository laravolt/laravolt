<?php namespace Laravolt\SemanticForm\Elements;

class Field extends Wrapper
{
    protected $attributes = [
        'class' => 'field'
    ];

    public function render()
    {
        $controls = '';
        $required = false;

        foreach ($this->controls as $control) {
            $controls .= $control;

            if($control->hasAttribute('required')) {
                $required = true;
            }
        }

        if($required) $this->addClass('required');

        $html = '<div';
        $html .= $this->renderAttributes();
        $html .= '>';

        $html .= $controls;

        $html .= $this->renderHelpBlock();

        $html .= '</div>';

        return $html;
    }
}
