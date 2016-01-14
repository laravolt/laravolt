<?php namespace Laravolt\SemanticForm\Elements;

abstract class Input extends FormControl
{
    public function render()
    {
        if ($this->label) {
            $element = clone $this;
            $element->label = false;
            return (new Field($this->label, $element))->render();
        }

        $result = '<input';

        $result .= $this->renderAttributes();

        $result .= '>';

        return $result;
    }

    public function value($value)
    {
        $this->setValue($value);

        return $this;
    }

    protected function setValue($value)
    {
        $this->setAttribute('value', $value);

        return $this;
    }
}
