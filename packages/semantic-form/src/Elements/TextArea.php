<?php

namespace Laravolt\SemanticForm\Elements;

class TextArea extends FormControl
{
    protected $attributes = [
        'name' => '',
        'rows' => 3,
    ];

    protected $value;

    public function render()
    {
        if ($this->label) {
            $element = clone $this;
            $element->label = false;

            return $this->decorateField(new Field($this->label, $element))->render();
        }

        $this->beforeRender();

        // Apply Preline/Tailwind default textarea classes if not provided
        $defaultClasses = 'py-2 px-3 sm:py-3 sm:px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600';
        $existing = $this->getAttribute('class');
        $this->addClass(trim(($existing ? $existing.' ' : '').$defaultClasses));
        if ($this->hasError()) {
            $this->addClass('border-red-500 focus:ring-red-500 focus:border-red-500');
        }

        $result = '<div class="sm:col-span-8 xl:col-span-4">';
        $result .= '<textarea';
        $result .= $this->renderAttributes();
        $result .= '>';
        $result .= form_escape($this->getValue());
        $result .= '</textarea>';
        $result .= $this->renderHint();
        $result .= $this->renderError();
        $result .= '</div>';

        return $result;
    }

    public function rows($rows)
    {
        $this->setAttribute('rows', $rows);

        return $this;
    }

    /** @deprecated */
    public function cols($cols)
    {
        $this->setAttribute('cols', $cols);

        return $this;
    }

    public function value($value)
    {
        $this->value = $value;

        return $this;
    }

    public function placeholder($placeholder)
    {
        $this->setAttribute('placeholder', $placeholder);

        return $this;
    }

    public function defaultValue($value)
    {
        if (! $this->hasValue()) {
            $this->value($value);
        }

        return $this;
    }

    protected function hasValue()
    {
        return isset($this->value);
    }
}
