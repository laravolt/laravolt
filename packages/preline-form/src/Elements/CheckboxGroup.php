<?php

namespace Laravolt\PrelineForm\Elements;

class CheckboxGroup extends Element
{
    protected $name;

    protected $options = [];

    protected $checkedValues = [];

    protected $hasError = false;

    protected $errorMessage = '';

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

    protected function renderControl()
    {
        $output = '<div class="space-y-2">';

        foreach ($this->options as $value => $label) {
            $checkbox = new Checkbox($this->name.'[]', $value);

            if (in_array($value, $this->checkedValues)) {
                $checkbox->checked(true);
            }

            if ($this->hasError) {
                $checkbox->setError();
            }

            $output .= '<div class="flex">';
            $output .= sprintf('<input%s>', $checkbox->renderAttributes());
            $output .= sprintf('<label class="text-sm text-gray-500 ms-3 dark:text-gray-400">%s</label>', form_escape($label));
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
