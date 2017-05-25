<?php
namespace Laravolt\SemanticForm\Elements;

class CheckboxGroup extends Wrapper
{

    protected $attributes = [
        'class' => 'grouped fields'
    ];

    protected $controls = [];

    /**
     * RadioGroup constructor.
     */
    public function __construct()
    {
        if (func_num_args() == 1 && is_array(func_get_arg(0))) {
            $this->controls = func_get_arg(0);
        } else {
            $this->controls = func_get_args();
        }
    }

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

        $html .= '</div>';

        return $html;
    }

    public function inline()
    {
        $this->setAttribute('class', 'inline fields');

        return $this;
    }
}
