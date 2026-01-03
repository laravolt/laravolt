<?php

namespace Laravolt\PrelineForm\Elements;

abstract class Input extends FormControl
{
    public function render()
    {
        if ($this->label) {
            $element = clone $this;
            $element->label = false;

            return $this->decorateField(new Field($this->label, $element))->render();
        }

        $this->beforeRender();

        $result = '<input';
        $result .= $this->renderAttributes();
        $result .= '>';
        $result .= $this->renderHint();

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

    protected function decorateField(Field $field)
    {
        if ($this->fieldCallback instanceof \Closure) {
            call_user_func($this->fieldCallback, $field);
        }

        return $field;
    }
}
