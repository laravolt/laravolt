<?php

namespace Laravolt\PrelineForm\Elements;

class Select extends Element
{
    protected $name;

    protected $options = [];

    protected $selectedValue;

    protected $hasError = false;

    protected $errorMessage = '';

    protected $placeholder;

    public function __construct($name, $options = [])
    {
        $this->name = $name;
        $this->options = $options;
        $this->setAttribute('name', $name);
        $this->setDefaultClasses();
    }

    protected function setDefaultClasses()
    {
        $this->addClass('py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600');
    }

    public function options($options)
    {
        $this->options = $options;

        return $this;
    }

    public function value($value)
    {
        $this->selectedValue = $value;

        return $this;
    }

    public function defaultValue($value)
    {
        if (is_null($this->selectedValue)) {
            $this->value($value);
        }

        return $this;
    }

    public function placeholder($placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function prependOption($key, $label)
    {
        $this->options = [$key => $label] + $this->options;

        return $this;
    }

    public function appendOption($key, $label)
    {
        $this->options[$key] = $label;

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

    protected function renderOptions()
    {
        $output = '';

        if ($this->placeholder) {
            $output .= sprintf('<option value="">%s</option>', form_escape($this->placeholder));
        }

        foreach ($this->options as $value => $label) {
            $selected = ($this->selectedValue == $value) ? ' selected' : '';
            $output .= sprintf(
                '<option value="%s"%s>%s</option>',
                form_escape($value),
                $selected,
                form_escape($label)
            );
        }

        return $output;
    }

    protected function renderControl()
    {
        return sprintf(
            '<select%s>%s</select>',
            $this->renderAttributes(),
            $this->renderOptions()
        );
    }

    public function render()
    {
        if ($this->label) {
            return $this->renderField();
        }

        return $this->renderControl();
    }
}
