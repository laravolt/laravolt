<?php

namespace Laravolt\SemanticForm\Elements;

use Illuminate\Support\Arr;

class Wrapper extends Element
{
    protected $controls = [];

    protected $openTag = '<div %s>';

    protected $closeTag = '</div>';

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
        $element = clone $this;

        if ($this->label) {
            $element->label = false;

            $field = $this->decorateField(new Field($this->label, $element));
            if ($control = $element->getPrimaryControl()) {
                $field->addClass($control->fieldWidth);
            }

            return $field->render();
        }

        $element->beforeRender();

        $html = sprintf($element->openTag, ltrim($element->renderAttributes()));

        foreach ($element->controls as $control) {
            $html .= $control;
        }

        $html .= $element->closeTag;
        $html .= $element->renderHint();

        return $html;
    }

    public function getControl($key)
    {
        return Arr::get($this->controls, $key, null);
    }

    public function addControl($control)
    {
        Arr::prepend($this->controls, $control);
    }

    public function getPrimaryControl()
    {
        foreach ($this->controls as $control) {
            if ($control instanceof FormControl) {
                return $control;
            }
        }

        return false;
    }

    public function required($required = true)
    {
        if ($required) {
            $this->setAttribute('required', 'required');
        } else {
            $this->removeAttribute('required');
        }

        $control = $this->getPrimaryControl();
        if ($control) {
            $control->required($required);
        }

        return $this;
    }

    public function value($value)
    {
        $control = $this->getPrimaryControl();
        if ($control instanceof Input) {
            $control->value($value);
        } else {
            $this->value($value);
        }

        return $this;
    }
}
