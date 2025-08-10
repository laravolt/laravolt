<?php

namespace Laravolt\SemanticForm\Elements;

class Checkbox extends Input
{
    protected $attributes = [
        'type' => 'checkbox',
    ];

    private $checked;

    protected $fieldLabel;

    public function __construct($name, $value = 1)
    {
        parent::__construct($name);
        $this->setValue($value);
    }

    public function render()
    {
        if ($this->label || $this->fieldLabel) {
            $element = clone $this;
            $element->label = false;
            $element->fieldLabel = false;
            $items = [];

            if (is_string($this->fieldLabel)) {
                $items[] = new Label($this->fieldLabel);
            }

            if ($this->label) {
                $items[] = new CheckboxWrapper($element, $this->label);
            }

            return $this->decorateField(new Field($items))->render();
        }

        $result = '<input';

        $result .= $this->renderAttributes();

        $result .= '>';

        return $result;
    }

    public function defaultToChecked()
    {
        if (! isset($this->checked)) {
            $this->check();
        }

        return $this;
    }

    public function defaultToUnchecked()
    {
        if (! isset($this->checked)) {
            $this->uncheck();
        }

        return $this;
    }

    public function defaultCheckedState($state)
    {
        $state ? $this->defaultToChecked() : $this->defaultToUnchecked();

        return $this;
    }

    public function check()
    {
        $this->setChecked(true);

        return $this;
    }

    public function uncheck()
    {
        $this->setChecked(false);

        return $this;
    }

    public function fieldLabel($label)
    {
        $this->fieldLabel = $label;

        return $this;
    }

    public function displayValue()
    {
        if ($this->checked) {
            return '<span class="inline-flex items-center rounded-md bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Ya</span>';
        }

        return '<span class="inline-flex items-center rounded-md bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">Tidak</span>';
    }

    public function setChecked($checked = true)
    {
        $this->checked = $checked;
        $this->removeAttribute('checked');

        if ($checked) {
            $this->setAttribute('checked', 'checked');
        }
    }
}
