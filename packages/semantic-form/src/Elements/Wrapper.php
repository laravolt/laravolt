<?php namespace Laravolt\SemanticForm\Elements;

use Illuminate\Support\Arr;

class Wrapper extends Element
{
    protected $controls = [];

    public function __construct()
    {
        $this->controls = func_get_args();
    }

    public function render()
    {

        if ($this->label) {
            $element = clone $this;
            $element->label = false;

            $field = (new Field($this->label, $element));
            if ($control = $element->getPrimaryControl()) {
                $field->addClass($control->fieldWidth);
            }

            return $field->render();
        }

        $this->beforeRender();

        $html = '<div';
        $html .= $this->renderAttributes();
        $html .= '>';

        foreach ($this->controls as $control) {
            $html .= $control;
        }

        $html .= '</div>';
        $html .= $this->renderHint();

        return $html;
    }

    public function getControl($key)
    {
        return Arr::get($this->controls, $key, null);
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

    public function required()
    {
        $this->setAttribute('required', 'required');

        $control = $this->getPrimaryControl();
        if ($control) {
            $control->required();
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
