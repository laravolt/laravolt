<?php

namespace Laravolt\PrelineForm\Elements;

class RadioButton extends Checkbox
{
    protected $attributes = [
        'type' => 'radio',
    ];

    public function __construct($name, $value = null)
    {
        parent::__construct($name);

        if (is_null($value)) {
            $value = $name;
        }

        $this->setValue($value);
        $this->setDefaultClasses();
    }

    protected function setDefaultClasses()
    {
        $this->addClass('shrink-0 mt-0.5 border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800');
    }

    public function checked($checked = true)
    {
        $this->checked = $checked;
        if ($checked) {
            $this->setAttribute('checked', 'checked');
        } else {
            unset($this->attributes['checked']);
        }

        return $this;
    }

    public function defaultChecked($checked = true)
    {
        if (! $this->checked) {
            $this->checked($checked);
        }

        return $this;
    }

    public function defaultCheckedState($checked = true)
    {
        return $this->defaultChecked($checked);
    }

    public function check()
    {
        return $this->checked(true);
    }

    public function uncheck()
    {
        return $this->checked(false);
    }



    public function displayValue()
    {
        return $this->isChecked ? $this->value : '';
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
