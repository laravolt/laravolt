<?php

declare(strict_types=1);

namespace Laravolt\PrelineForm\Elements;

class SelectMultiple extends Select
{
    protected $selectedValues = [];

    public function __construct($name, $options = [])
    {
        parent::__construct($name, $options);
        $this->setAttribute('multiple', 'multiple');
        // Change name to array format for multiple selection
        $this->setAttribute('name', $name.'[]');
    }

    public function value($values)
    {
        $this->selectedValues = is_array($values) ? $values : [$values];

        return $this;
    }

    public function defaultValue($values)
    {
        if (empty($this->selectedValues)) {
            $this->value($values);
        }

        return $this;
    }

    protected function renderOptions()
    {
        $output = '';

        if ($this->placeholder) {
            $output .= sprintf('<option value="" disabled>%s</option>', form_escape($this->placeholder));
        }

        foreach ($this->options as $value => $label) {
            $selected = in_array($value, $this->selectedValues) ? ' selected' : '';
            $output .= sprintf(
                '<option value="%s"%s>%s</option>',
                form_escape($value),
                $selected,
                form_escape($label)
            );
        }

        return $output;
    }
}
