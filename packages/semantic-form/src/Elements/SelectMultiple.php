<?php

namespace Laravolt\SemanticForm\Elements;

class SelectMultiple extends Select
{
    protected $selected;

    protected $attributes = [
        'class' => 'block w-full rounded-lg border-gray-200 text-gray-800 text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300',
        'multiple' => 'multiple',
    ];

    public function defaultValue($value)
    {
        if (empty($this->selected)) {
            $this->select($value);
        }

        return $this;
    }

    public function select($selected)
    {
        $selected = (array) $selected;
        $this->selected = $selected ?? [];

        if (! empty($selected)) {
            $this->data('value', implode(',', $this->selected));
        }

        return $this;
    }

    public function render()
    {
        if ($this->label) {
            $element = clone $this;
            $element->label = false;

            return $this->decorateField(new Field($this->label, $element))->render();
        }

        $result = '<select';
        $result .= $this->renderAttributes();
        $result .= '>';
        $result .= $this->renderOptions();
        $result .= '</select>';
        $result .= $this->renderHint();

        return $result;
    }
}
