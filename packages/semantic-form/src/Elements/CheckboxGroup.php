<?php
namespace Laravolt\SemanticForm\Elements;

class CheckboxGroup extends Wrapper
{

    protected $attributes = [
        'class' => 'grouped fields'
    ];

    protected $controls = [];
    
    public function render()
    {
        $html = '<div';
        $html .= $this->renderAttributes();
        $html .= '>';

        if ($this->label) {
            $html .= $this->label;
        }

        foreach ($this->controls as $control) {
            $html .= $control;
        }

        $html .= $this->renderHint();
        $html .= '</div>';

        return $html;
    }

    public function inline()
    {
        $this->setAttribute('class', 'inline fields');

        return $this;
    }
}
