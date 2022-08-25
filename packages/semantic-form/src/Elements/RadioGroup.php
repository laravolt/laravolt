<?php

namespace Laravolt\SemanticForm\Elements;

class RadioGroup extends CheckboxGroup
{
    public function setChecked($value)
    {
        $this->value($value);

        /** @var $control \Laravolt\SemanticForm\Elements\RadioButton */
        foreach ($this->controls as $control) {
            if ($control->getValue() === $value) {
                $control->check();
            }
        }

        return $this;
    }
}
