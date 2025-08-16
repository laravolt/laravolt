<?php

namespace Laravolt\PrelineForm\Elements;

class File extends Element
{
    protected $name;

    protected $hasError = false;

    protected $errorMessage = '';

    public function __construct($name)
    {
        $this->name = $name;
        $this->setAttribute('type', 'file');
        $this->setAttribute('name', $name);
        $this->setDefaultClasses();
    }

    protected function setDefaultClasses()
    {
        $this->addClass('block w-full border border-gray-200 shadow-sm rounded-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600 file:bg-gray-50 file:border-0 file:me-4 file:py-3 file:px-4 dark:file:bg-gray-700 dark:file:text-gray-400');
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
