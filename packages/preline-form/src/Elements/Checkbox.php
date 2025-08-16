<?php

namespace Laravolt\PrelineForm\Elements;

class Checkbox extends Element
{
    protected $name;
    protected $value;
    protected $checked = false;
    protected $hasError = false;
    protected $errorMessage = '';

    public function __construct($name, $value = 1)
    {
        $this->name = $name;
        $this->value = $value;
        $this->setAttribute('type', 'checkbox');
        $this->setAttribute('name', $name);
        $this->setAttribute('value', $value);
        $this->setDefaultClasses();
    }

    protected function setDefaultClasses()
    {
        $this->addClass('shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800');
    }

    public function checked($checked = true)
    {
        if ($checked == $this->value || $checked === true) {
            $this->checked = true;
            $this->setAttribute('checked', 'checked');
        } else {
            $this->checked = false;
            unset($this->attributes['checked']);
        }

        return $this;
    }

    public function defaultChecked($checked = true)
    {
        if (!$this->checked) {
            $this->checked($checked);
        }

        return $this;
    }

    public function setError($message = '')
    {
        $this->hasError = true;
        $this->errorMessage = $message;
        $this->removeClass('border-gray-200 focus:ring-blue-500');
        $this->addClass('border-red-500 focus:ring-red-500');

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
        $output = sprintf('<input%s>', $this->renderAttributes());
        
        if ($this->label) {
            $labelText = $this->label instanceof Label ? $this->label->render() : $this->label;
            $output = sprintf(
                '<div class="flex"><input%s><label class="text-sm text-gray-500 ms-3 dark:text-gray-400">%s</label></div>',
                $this->renderAttributes(),
                $labelText
            );
        }

        return $output;
    }

    public function render()
    {
        if ($this->label) {
            $output = '<div class="space-y-1">';
            $output .= $this->renderControl();
            $output .= $this->renderError();
            $output .= $this->renderHint();
            $output .= '</div>';
            return $output;
        }

        return $this->renderControl();
    }
}