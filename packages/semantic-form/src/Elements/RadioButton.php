<?php

namespace Laravolt\SemanticForm\Elements;

class RadioButton extends Checkbox
{
    protected $attributes = [
        'type' => 'radio',
    ];

    public function __construct($name, $value = null)
    {
        parent::__construct($name);

        if (is_null($value)) {
            $value = $name;
        }

        $this->setValue($value);
    }

    public function render()
    {
        if ($this->label || $this->fieldLabel) {
            $element = clone $this;
            $element->label = false;
            $element->fieldLabel = false;
            $items = [];

            if (is_string($this->fieldLabel)) {
                $items[] = new Label($this->fieldLabel);
            }

            if ($this->label) {
                $items[] = new RadioButtonWrapper($element, $this->label);
            }

            return $this->decorateField(new Field($items))->render();
        }

        $result = '<input';

        $result .= $this->renderAttributes();

        $result .= '>';

        return $result;
    }
}
