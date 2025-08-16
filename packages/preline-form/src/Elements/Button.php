<?php

namespace Laravolt\PrelineForm\Elements;

class Button extends Element
{
    protected $value;

    protected $type;

    public function __construct($value, $type = 'button')
    {
        $this->value = $value;
        $this->type = $type;
        $this->setAttribute('type', $type);
        $this->setDefaultClasses();
    }

    protected function setDefaultClasses()
    {
        if ($this->type === 'submit') {
            $this->addClass('py-3 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600');
        } else {
            $this->addClass('py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600');
        }
    }

    public function primary()
    {
        $this->removeClass('py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600');
        $this->addClass('py-3 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600');

        return $this;
    }

    public function secondary()
    {
        $this->removeClass('py-3 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600');
        $this->addClass('py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600');

        return $this;
    }

    public function danger()
    {
        $this->removeClass('bg-blue-600 hover:bg-blue-700');
        $this->addClass('bg-red-600 hover:bg-red-700');

        return $this;
    }

    public function success()
    {
        $this->removeClass('bg-blue-600 hover:bg-blue-700');
        $this->addClass('bg-green-600 hover:bg-green-700');

        return $this;
    }

    public function render()
    {
        return sprintf('<button%s>%s</button>', $this->renderAttributes(), form_escape($this->value));
    }
}
