<?php

namespace Laravolt\PrelineForm\Elements;

class File extends Input
{
    protected $attributes = [
        'type' => 'file',
    ];

    protected $errorMessage = '';

    public function __construct($name)
    {
        parent::__construct($name);
        $this->setDefaultClasses();
    }

    protected function setDefaultClasses()
    {
        $this->addClass('block w-full border border-gray-200 shadow-sm rounded-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-300 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-neutral-600 file:bg-gray-50 file:border-0 file:me-4 file:py-3 file:px-4 dark:file:bg-neutral-700 dark:file:text-neutral-300');
    }

    public function multiple()
    {
        $this->setAttribute('multiple', 'multiple');

        return $this;
    }

    public function accept($accept)
    {
        $this->setAttribute('accept', $accept);

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

    public function render()
    {
        if ($this->label) {
            return $this->renderField();
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
