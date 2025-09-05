<?php

namespace Laravolt\PrelineForm\Elements;

class CheckboxGroup extends Element
{
    protected $name;

    protected $options = [];

    protected $checkedValues = [];

    protected $hasError = false;

    protected $errorMessage = '';

    protected $inline = false;

    public function __construct($name, $options = [])
    {
        $this->name = $name;
        $this->options = $options;
    }

    public function options($options)
    {
        $this->options = $options;

        return $this;
    }

    public function checked($values)
    {
        $this->checkedValues = is_array($values) ? $values : [$values];

        return $this;
    }

    public function defaultChecked($values)
    {
        if (empty($this->checkedValues)) {
            $this->checked($values);
        }

        return $this;
    }

    public function setChecked($values)
    {
        return $this->checked($values);
    }

    public function inline($inline = true)
    {
        $this->inline = $inline;

        return $this;
    }

    public function setError($message = '')
    {
        $this->hasError = true;
        $this->errorMessage = $message;

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

    public function displayValue()
    {
        if (! is_array($this->checkedValues)) {
            return '';
        }

        $labels = [];
        foreach ($this->checkedValues as $value) {
            $labels[] = $this->options[$value] ?? $value;
        }

        return implode(', ', $labels);
    }

    protected function renderControl()
    {
        $output = '<div class="space-y-2">';

        foreach ($this->options as $value => $label) {
            $id = $this->name . '_' . $value . '_' . uniqid();
            $checkbox = new Checkbox($this->name.'[]', $value);
            $checkbox->attributes(compact('id'));

            if (in_array($value, $this->checkedValues)) {
                $checkbox->checked(true);
            }

            if ($this->hasError) {
                $checkbox->setError();
            }

            $output .= '<div class="flex">';
            $output .= sprintf('<input%s>', $checkbox->renderAttributes());
            $output .= sprintf('<label class="text-sm text-gray-500 ms-3 dark:text-gray-400" for="%s">%s</label>', $id, form_escape($label));
            $output .= '</div>';
        }

        $output .= '</div>';

        return $output;
    }

    public function render()
    {
        if ($this->label) {
            return $this->renderField();
        }

        return $this->renderControl();
    }
}
