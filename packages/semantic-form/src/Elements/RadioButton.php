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

        // Tailwind/Preline radio classes
        $defaultClasses = 'shrink-0 mt-0.5 border-gray-200 text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800';
        // $defaultClasses = 'text-sm text-gray-500 ms-2 dark:text-neutral-400';
        $existing = $this->getAttribute('class');
        $this->addClass(trim(($existing ? $existing.' ' : '').$defaultClasses));

        $result = '<input';

        $result .= $this->renderAttributes();

        $result .= ' />';

        return $result;
    }
}
