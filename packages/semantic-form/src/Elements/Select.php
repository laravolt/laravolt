<?php

namespace Laravolt\SemanticForm\Elements;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Select extends FormControl
{
    protected $options = [];

    protected $selected;

    protected $attributes = [
        'class' => 'ui dropdown search',
    ];

    public function __construct($name, $options = [])
    {
        if ($options instanceof Collection) {
            $options = $options->toArray();
        }

        $this->setName($name);
        $this->setOptions($options);
    }

    public function select($option)
    {
        $this->selected = $option;

        return $this;
    }

    public function value($value)
    {
        $this->value = $value;

        return $this->select($value);
    }

    protected function setOptions($options)
    {
        $this->options = $options;
    }

    public function options($options)
    {
        $this->setOptions($options);

        return $this;
    }

    public function render()
    {
        if ($this->label) {
            $element = clone $this;
            $element->label = false;

            $field = new Field($this->label, $element);
            if ($element->getAttribute('readonly')) {
                $field->addClass('disabled readonly');
            }

            return $this->decorateField($field)->render();
        }

        $this->beforeRender();

        $result = '<select';
        $result .= $this->renderAttributes();
        $result .= '>';
        $result .= $this->renderOptions();
        $result .= '</select>';
        $result .= $this->renderHint();

        return $result;
    }

    protected function renderOptions()
    {
        $result = '';

        foreach ($this->options as $value => $label) {
            if (is_array($label)) {
                $result .= $this->renderOptGroup($value, $label);
                continue;
            }
            $result .= $this->renderOption($value, $label);
        }

        return $result;
    }

    protected function renderOptGroup($label, $options)
    {
        $result = "<optgroup label=\"{$label}\">";
        foreach ($options as $value => $label) {
            $result .= $this->renderOption($value, $label);
        }
        $result .= '</optgroup>';

        return $result;
    }

    protected function renderOption($value, $label)
    {
        $option = '<option ';
        $option .= 'value="'.$value.'"';
        $option .= $this->isSelected($value) ? ' selected' : '';
        $option .= '>';
        $option .= $label;
        $option .= '</option>';

        return $option;
    }

    protected function isSelected($value)
    {
        $selected = $this->selected;

        if (is_string($selected)) {
            $selected = html_entity_decode($selected);
        }

        return in_array($value, (array) $selected);
    }

    public function addOption($value, $label)
    {
        $this->options[$value] = $label;

        return $this;
    }

    public function prependOption($value, $label)
    {
        $this->options = Arr::prepend($this->options, $label, $value);

        return $this;
    }

    public function appendOption($value, $label)
    {
        $this->options = Arr::add($this->options, $value, $label);

        return $this;
    }

    public function placeholder($label = '-- Select --')
    {
        return $this->prependOption('', $label);
    }

    public function defaultValue($value)
    {
        if ($this->selected !== null) {
            return $this;
        }

        $this->select($value);

        return $this;
    }

    public function multiple()
    {
        $name = $this->attributes['name'];
        if (substr($name, -2) != '[]') {
            $name .= '[]';
        }

        $this->setName($name);
        $this->setAttribute('multiple', 'multiple');

        return $this;
    }

    public function displayValue()
    {
        if (is_string($this->value)) {
            return Arr::get($this->options, $this->value);
        }

        return null;
    }
}
