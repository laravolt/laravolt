<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

use Closure;
use Illuminate\Support\Arr;
use Laravolt\SemanticForm\SemanticForm;

class CheckboxGroup extends Wrapper
{
    protected $value;

    protected $options;

    protected $attributes = [
        'class' => 'grouped fields',
    ];

    protected $controls = [];

    public function inline($inline = true)
    {
        if ($inline) {
            $this->setAttribute('class', 'inline fields');
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
                return '<div class="ui label basic green">Ya</div>';
            }

            return '<div class="ui label basic red">Tidak</div>';
        }

        if (is_string($val)) {
            $option = Arr::get($this->options, $val);

            return $option['label'] ?? $option;
        }

        if (is_array($val)) {
            $output = '';
            foreach ($val as $v) {
                $output .= "<div class='ui label $color'>$v</div>";
            }

            return $output;
        }

        return SemanticForm::$displayNullValueAs;
    }

    public function attributes($attributes)
    {
        foreach ($this->controls as $control) {
            if ($control instanceof Checkbox) {
                if ($attributes instanceof Closure) {
                    $attributes($control);
                } else {
                    $control->attributes($attributes);
                }
            }
        }

        return $this;
    }
}
