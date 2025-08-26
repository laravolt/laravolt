<?php

namespace Laravolt\PrelineForm\Elements;

class Text extends Element
{
    protected $name;

    protected $hasError = false;

    protected $errorMessage = '';

    public function __construct($name)
    {
        $this->name = $name;
        $this->setAttribute('type', 'text');
        $this->setAttribute('name', $name);
        $this->setDefaultClasses();
    }

    protected function setDefaultClasses()
    {
        $this->addClass('py-1.5 sm:py-2 px-3 block w-full border-gray-200 rounded-lg sm:text-sm placeholder:text-gray-400 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:placeholder:text-white/60 dark:focus:ring-neutral-600');
    }

    public function value($value)
    {
        $this->setAttribute('value', $value);

        return $this;
    }

    public function defaultValue($value)
    {
        if (is_null($this->getAttribute('value'))) {
            $this->value($value);
        }

        return $this;
    }

    public function placeholder($placeholder)
    {
        $this->setAttribute('placeholder', $placeholder);

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

    protected function getError()
    {
        return $this->errorMessage;
    }

    protected function renderControl()
    {
        return sprintf('<input%s>', $this->renderAttributes());
    }

    public function render()
    {
        if ($this->label) {
            return $this->renderField();
        }

        return $this->renderControl();
    }
}
