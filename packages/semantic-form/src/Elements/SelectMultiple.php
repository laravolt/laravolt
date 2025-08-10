<?php

namespace Laravolt\SemanticForm\Elements;

class SelectMultiple extends Select
{
    protected $selected;

    protected $attributes = [
        'class' => 'py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600',
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
