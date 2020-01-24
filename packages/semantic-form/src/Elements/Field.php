<?php

namespace Laravolt\SemanticForm\Elements;

class Field extends Wrapper
{
    protected $attributes = [
        'class' => 'field',
    ];

    public function render()
    {
        $controls = '';
        $required = false;
        $hasError = false;

        foreach ($this->controls as $control) {
            $controls .= $control;

            if ($control->getAttribute('required')) {
                $required = true;
            }

            if ($control instanceof InputWrapper) {
                if ($control->isRequired()) {
                    $required = true;
                }
                if ($control->hasError()) {
                    $hasError = true;
                }

                $this->attributes($control->getFieldAttributes());
            }

            if ($control instanceof CheckboxWrapper || $control instanceof RadioButtonWrapper) {
                $this->attributes($control->getPrimaryControl()->getFieldAttributes());
            }

            if ($control instanceof CheckboxGroup || $control instanceof RadioGroup) {
                $this->attributes($control->getFieldAttributes());
            }

            if ($control instanceof FormControl) {
                if ($control->hasError()) {
                    $hasError = true;
                }
                $this->attributes($control->getFieldAttributes());
            }

            if ($control instanceof Html) {
                $this->attributes($control->getFieldAttributes());
            }
        }

        if ($required) {
            $this->addClass('required');
        }
        if ($hasError) {
            $this->addClass('error');
        }

        $html = '<div';
        $html .= $this->renderAttributes();
        $html .= '>';
        $html .= $controls;
        $html .= '</div>';

        return $html;
    }
}
