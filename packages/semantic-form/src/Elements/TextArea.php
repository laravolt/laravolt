<?php

namespace Laravolt\SemanticForm\Elements;

class TextArea extends FormControl
{
    protected $attributes = [
        'name' => '',
        'rows' => 10,
        'cols' => 50,
        'class' => 'block w-full rounded-lg border-gray-200 text-gray-800 text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300',
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

        $result = '<textarea';
        $result .= $this->renderAttributes();
        $result .= '>';
        $result .= form_escape($this->getValue());
        $result .= '</textarea>';
        $result .= $this->renderHint();

        return $result;
    }

    public function rows($rows)
    {
        $this->setAttribute('rows', $rows);

        return $this;
    }

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
