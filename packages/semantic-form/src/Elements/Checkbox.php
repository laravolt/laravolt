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
        if (!isset($this->checked)) {
            $this->check();
        }

        return $this;
    }

    public function defaultToUnchecked()
    {
        if (!isset($this->checked)) {
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
            return '<div class="ui tiny label basic green">Ya</div>';
        }

        return '<div class="ui tiny label basic red">Tidak</div>';
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
