<?php

namespace Laravolt\SemanticForm\Elements;

use Illuminate\Support\Arr;
use Laravolt\SemanticForm\SemanticForm;

class CheckboxGroup extends Wrapper
{
    protected $value;

    protected $options;

    protected $attributes = [
        'class' => 'space-y-2',
    ];

    protected $controls = [];

    public function inline($inline = true)
    {
        if ($inline) {
            $this->setAttribute('class', 'flex flex-wrap gap-4');
        }

        return $this;
    }

    public function value($value)
    {
        $this->value = $value;

        return $this;
    }

    public function options($options)
    {
        $this->options = $options;
    }

    public function setChecked($values)
    {
        $this->value($values);

        $values = (array) ($values);
        /** @var $control \Laravolt\SemanticForm\Elements\Checkbox */
        foreach ($this->controls as $control) {
            if (in_array($control->getValue(), $values)) {
                $control->check();
            }
        }

        return $this;
    }

    public function displayValue()
    {
        $color = config('laravolt.ui.color');
        $val = $this->value;

        if (is_bool($val)) {
            $val = (int) $val;
            if ($val) {
                return '<span class="inline-flex items-center rounded-md bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Ya</span>';
            }

            return '<span class="inline-flex items-center rounded-md bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">Tidak</span>';
        }

        if (is_string($val)) {
            $option = Arr::get($this->options, $val);

            return $option['label'] ?? $option;
        }

        if (is_array($val)) {
            $output = '';
            foreach ($val as $v) {
                $output .= "<span class='inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-500/10'>$v</span>";
            }

            return $output;
        }

        return SemanticForm::$displayNullValueAs;
    }

    public function attributes($attributes)
    {
        foreach ($this->controls as $control) {
            if ($control instanceof Checkbox) {
                if ($attributes instanceof \Closure) {
                    $attributes($control);
                } else {
                    $control->attributes($attributes);
                }
            }
        }

        return $this;
    }
}
