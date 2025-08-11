<?php

namespace Laravolt\SemanticForm\Elements;

class RadioGroup extends CheckboxGroup
{
    public function setChecked($value)
    {
        $this->value($value);

        /** @var \Laravolt\SemanticForm\Elements\RadioButton $control */
        foreach ($this->controls as $control) {
            if ($control->getValue() === $value) {
                $control->check();
            }
        }

        return $this;
    }

    public function render()
    {
        // Base container classes; inline() from parent can override with wrap + gaps
        $this->addClass('flex gap-x-6');

        // Guess group name from first radio control if present
        $groupName = '';
        foreach ($this->controls as $control) {
            if ($control instanceof RadioButton) {
                $groupName = $control->getAttribute('name');
                break;
            }
        }

        $html = '<div';
        $html .= $this->renderAttributes();
        $html .= '>';

        $index = 1;
        foreach ($this->options as $value => $label) {
            $idBase = preg_replace('/[^a-zA-Z0-9_-]+/', '-', $groupName ?: 'radio-group');
            $id = $idBase.'-'.$index;
            $isChecked = in_array($value, (array) $this->value, true);

            $inputClasses = 'shrink-0 mt-0.5 border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 checked:border-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800';

            $html .= '<div class="flex">';
            $html .= sprintf(
                '<input type="radio" name="%s" class="%s" id="%s" value="%s"%s>',
                e($groupName),
                $inputClasses,
                e($id),
                e($value),
                $isChecked ? ' checked' : ''
            );
            $html .= sprintf(
                '<label for="%s" class="text-sm text-gray-500 ms-2 dark:text-neutral-400">%s</label>',
                e($id),
                form_escape(is_array($label) ? ($label['label'] ?? '') : $label)
            );
            $html .= '</div>';
            $index++;
        }

        $html .= '</div>';

        /** @var \Laravolt\SemanticForm\Elements\Label */
        $label = $this->label;

        // If group has a label set via ->label(), wrap using Field to render label + hint
        if ($label) {
            $label->removeAttribute('for');
            $field = new Field($label, new Html($html));
            return $this->decorateField($field)->render();
        }

        return $html;
    }
}
