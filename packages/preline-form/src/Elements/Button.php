<?php

namespace Laravolt\PrelineForm\Elements;

class Button extends FormControl
{
    protected $attributes = [
        'type' => 'button',
    ];

    protected $text;

    protected $type;

    public function __construct($text, $name = null, $type = 'button')
    {
        parent::__construct($name);
        
        $this->text = $text;
        $this->type = $type;
        $this->setAttribute('type', $type);
        $this->setDefaultClasses();
    }

    protected function setDefaultClasses()
    {
        if ($this->type === 'submit') {
            $this->addClass('py-2 px-3 inline-flex justify-center items-center gap-x-2 text-start bg-blue-600 border border-blue-600 text-white text-sm font-medium rounded-lg shadow-2xs align-middle hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:ring-1 focus:ring-blue-300 dark:focus:ring-blue-500');
        } else {
            $this->addClass('py-2 px-3 inline-flex justify-center items-center text-start bg-white border border-gray-200 text-gray-800 text-sm font-medium rounded-lg shadow-2xs align-middle hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700');
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

    public function text($text)
    {
        $this->text = $text;

        return $this;
    }

    public function render()
    {
        $this->beforeRender();

        return sprintf('<button%s>%s</button>', $this->renderAttributes(), form_escape($this->text));
    }
}
