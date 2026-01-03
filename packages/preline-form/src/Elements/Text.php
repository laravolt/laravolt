<?php

namespace Laravolt\PrelineForm\Elements;

class Text extends Input
{
    protected $attributes = [
        'type' => 'text',
    ];

    protected $errorMessage = '';

    public function __construct($name)
    {
        parent::__construct($name);
        $this->setDefaultClasses();
    }

    protected function setDefaultClasses()
    {
        $this->addClass('py-1.5 sm:py-2 px-3 block w-full border-gray-200 rounded-lg sm:text-sm placeholder:text-gray-400 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:placeholder:text-white/60 dark:focus:ring-neutral-600');
    }

    public function value($value)
    {
        $this->setValue($value);

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
        parent::setError();
        $this->errorMessage = $message;
        $this->removeClass('border-gray-200 focus:border-blue-500 focus:ring-blue-500');
        $this->addClass('border-red-500 focus:border-red-500 focus:ring-red-500');

        return $this;
    }

    public function hasError()
    {
        return parent::hasError();
    }

    protected function getError()
    {
        return $this->errorMessage;
    }

    protected function hasValue()
    {
        return isset($this->attributes['value']);
    }

    public function render()
    {
        $idAttribute = $this->getAttribute('id') ?? md5($this->getAttribute('name'));

        $this->attribute('id', $idAttribute);

        if ($this->label) {
            return $this->renderField($idAttribute);
        }

        $this->beforeRender();

        $result = '<input';
        $result .= $this->renderAttributes();
        $result .= '>';
        $result .= $this->renderHint();

        return $result;
    }

    protected function renderControl()
    {
        return sprintf('<input%s>', $this->renderAttributes());
    }
}
