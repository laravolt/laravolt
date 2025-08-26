<?php

namespace Laravolt\PrelineForm\Elements;

class Select extends FormControl
{
    protected $options = [];

    protected $selectedValue;

    protected $errorMessage = '';

    protected $placeholder;

    public function __construct($name, $options = [])
    {
        parent::__construct($name);
        $this->options = $options;
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

    public function select($value)
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

    public function addOption($value, $label)
    {
        $this->options[$value] = $label;

        return $this;
    }

    public function multiple()
    {
        $name = $this->getAttribute('name');
        if (substr($name, -2) != '[]') {
            $name .= '[]';
        }

        $this->setAttribute('name', $name);
        $this->setAttribute('multiple', 'multiple');

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

    public function displayValue()
    {
        if (is_string($this->selectedValue) || is_int($this->selectedValue)) {
            return $this->options[$this->selectedValue] ?? $this->selectedValue;
        }

        return null;
    }

    public function render()
    {
        if ($this->label) {
            return $this->renderField();
        }

        return $this->renderControl();
    }
}
