<?php

namespace Laravolt\SemanticForm\Elements;

use Laravolt\SemanticForm\Elements\Label;

abstract class Input extends FormControl
{
    public function render()
    {
        if ($this->label) {
            $element = clone $this;
            $element->label = false;

            /** @var Label */
            $label = $this->label;
            $label->setAttribute('for', $this->idAttribute);

            $field = new Field($label, $element);

            return $this->decorateField($field)
                ->addClass($this->fieldWidth)
                ->render();
        }

        $this->beforeRender();

        // Apply Preline/Tailwind default input classes if not provided
        $defaultClasses = 'py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600';
        $existing = $this->getAttribute('class');
        $this->addClass(trim(($existing ? $existing.' ' : '').$defaultClasses));

        if ($this->hasError()) {
            $this->addClass('border-red-500 focus:ring-red-500 focus:border-red-500');
        }

        $this->attribute('id', $this->idAttribute);

        $result = '<input';
        $result .= $this->renderAttributes();
        $result .= ' />';
        $result .= $this->renderHint();
        $result .= $this->renderError();

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
