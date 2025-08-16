<?php

namespace Laravolt\PrelineForm\Elements;

class TextArea extends Element
{
    protected $name;

    protected $value;

    protected $hasError = false;

    protected $errorMessage = '';

    public function __construct($name)
    {
        $this->name = $name;
        $this->setAttribute('name', $name);
        $this->setDefaultClasses();
    }

    protected function setDefaultClasses()
    {
        $this->addClass('py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600');
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
        $this->hasError = true;
        $this->errorMessage = $message;
        $this->removeClass('border-gray-200 focus:border-blue-500 focus:ring-blue-500');
        $this->addClass('border-red-500 focus:border-red-500 focus:ring-red-500');

        return $this;
    }

    protected function hasError()
    {
        return $this->hasError;
    }

    protected function getError()
    {
        return $this->errorMessage;
    }

    protected function renderControl()
    {
        return sprintf('<textarea%s>%s</textarea>', $this->renderAttributes(), form_escape($this->value ?? ''));
    }

    public function render()
    {
        if ($this->label) {
            return $this->renderField();
        }

        return $this->renderControl();
    }
}
