<?php

namespace Laravolt\PrelineForm\Elements;

class RadioGroup extends Element
{
    protected $name;

    protected $options = [];

    protected $checkedValue;

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

    public function checked($value)
    {
        $this->checkedValue = $value;

        return $this;
    }

    public function defaultChecked($value)
    {
        if (is_null($this->checkedValue)) {
            $this->checked($value);
        }

        return $this;
    }

    public function setChecked($value)
    {
        return $this->checked($value);
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
            $radio = new RadioButton($this->name, $value);

            if ($this->checkedValue == $value) {
                $radio->checked(true);
            }

            if ($this->hasError) {
                $radio->setError();
            }

            $output .= '<div class="flex">';
            $output .= sprintf('<input%s>', $radio->renderAttributes());
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
