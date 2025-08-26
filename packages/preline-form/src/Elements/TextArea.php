<?php

namespace Laravolt\PrelineForm\Elements;

class TextArea extends FormControl
{
    protected $attributes = [
        'rows' => 10,
        'cols' => 50,
    ];

    protected $errorMessage = '';

    public function __construct($name)
    {
        parent::__construct($name);
        $this->setDefaultClasses();
    }

    protected function setDefaultClasses()
    {
        $this->addClass('py-2 px-3 sm:py-3 sm:px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:placeholder:text-white/60 dark:focus:ring-neutral-600');
    }

    public function value($value)
    {
        $this->value = $value;

        return $this;
    }

    public function defaultValue($value)
    {
        if (is_null($this->value)) {
            $this->value($value);
        }

        return $this;
    }

    public function placeholder($placeholder)
    {
        $this->setAttribute('placeholder', $placeholder);

        return $this;
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

    public function setError($message = '')
    {
        parent::setError(true);
        $this->errorMessage = $message;
        $this->removeClass('border-gray-200 focus:border-blue-500 focus:ring-blue-500');
        $this->addClass('border-red-500 focus:border-red-500 focus:ring-red-500');

        return $this;
    }

    public function hasError()
    {
        return parent::hasError();
    }

    protected function hasValue()
    {
        return isset($this->value);
    }

    protected function getError()
    {
        return $this->errorMessage;
    }

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

    protected function renderControl()
    {
        return sprintf('<textarea%s>%s</textarea>', $this->renderAttributes(), form_escape($this->value ?? ''));
    }

    protected function decorateField(Field $field)
    {
        if ($this->fieldCallback instanceof \Closure) {
            call_user_func($this->fieldCallback, $field);
        }

        return $field;
    }
}
