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
                $items[] = (new Label($this->fieldLabel))->addClass('ms-3');
            }

            if ($this->label) {
                $items[] = new CheckboxWrapper($element, $this->label);
            }

            return $this->decorateField(new Field($items))->render();
        }

        // Tailwind/Preline checkbox classes
        $defaultClasses = 'shrink-0 mt-0.5 border-gray-200 rounded-sm text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800';
        $existing = $this->getAttribute('class');
        $this->addClass(trim(($existing ? $existing.' ' : '').$defaultClasses));

        $result = '<input';

        $result .= $this->renderAttributes();

        $result .= ' />';

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
            return '<div class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 rounded dark:bg-green-900/30 dark:text-green-400">Ya</div>';
        }

        return '<div class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-red-100 text-red-800 rounded dark:bg-red-900/30 dark:text-red-400">Tidak</div>';
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
