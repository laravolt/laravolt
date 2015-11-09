<?php namespace Laravolt\SemanticForm\Elements;

use AdamWathan\Form\Elements\Element;
use AdamWathan\Form\Elements\Label;

class CheckboxGroup extends Element
{
    protected $label;
    protected $controls;

    public function __construct(Label $label, $controls)
    {
        $this->label = $label;
        $this->controls = $controls;
        $this->addClass('fields grouped');
    }

    public function render()
    {
        $html  = '<div';
        $html .= $this->renderAttributes();
        $html .= '>';
        $html .=  $this->label;

        foreach($this->controls as $control) {
            $html .=  $control;
        }

        $html .= '</div>';

        return $html;
    }

    public function label()
    {
        return $this->label;
    }
}
