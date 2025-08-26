<?php

namespace Laravolt\PrelineForm\Elements;

use Illuminate\Support\Arr;

abstract class Element
{
    protected $attributes = [];

    protected $fieldAttributes = [];

    protected $label = false;

    protected $fieldCallback = null;

    protected $hint = [];

    protected function getPrimaryControl()
    {
        return $this;
    }

    protected function setAttribute($attribute, $value = null)
    {
        if (is_null($value)) {
            return;
        }

        $this->attributes[$attribute] = $value;
    }

    protected function getAttribute($attribute)
    {
        return Arr::get($this->attributes, $attribute);
    }

    protected function removeAttribute($attribute)
    {
        unset($this->attributes[$attribute]);
    }

    public function hasAttribute($attribute)
    {
        return isset($this->attributes[$attribute]);
    }

    public function clear($attribute)
    {
        if (! isset($this->attributes[$attribute])) {
            return $this;
        }

        $this->removeAttribute($attribute);

        return $this;
    }

    public function addClass($class)
    {
        $existingClasses = $this->getAttribute('class');
        if ($existingClasses) {
            $this->setAttribute('class', $existingClasses.' '.$class);
        } else {
            $this->setAttribute('class', $class);
        }

        return $this;
    }

    public function addClassIf($condition, $class)
    {
        if ($condition) {
            $this->addClass($class);
        }

        return $this;
    }

    public function removeClass($class)
    {
        $existingClasses = $this->getAttribute('class');
        if ($existingClasses) {
            $classes = explode(' ', $existingClasses);
            // Remove all classes that contain the $class substring
            $classes = array_filter($classes, function ($c) use ($class) {
                return strpos($c, $class) === false;
            });
            $this->setAttribute('class', implode(' ', $classes));
        }

        return $this;
    }

    public function attribute($attribute, $value = null)
    {
        $this->setAttribute($attribute, $value);

        return $this;
    }

    public function attributes($attributes)
    {
        foreach ($attributes as $attribute => $value) {
            if ($attribute == 'class') {
                $this->addClass($value);
            } else {
                $this->setAttribute($attribute, $value);
            }
        }

        return $this;
    }

    public function data($attribute, $value = null)
    {
        $this->setAttribute('data-'.$attribute, $value);

        return $this;
    }

    public function id($id)
    {
        $this->setId($id);

        return $this;
    }

    public function fieldWidth($width)
    {
        // For Preline compatibility - can be enhanced as needed
        return $this;
    }

    protected function setId($id)
    {
        $this->setAttribute('id', $id);
    }

    public function label($label, ?\Closure $callback = null)
    {
        if ($label) {
            $this->label = new Label($label);
            $this->fieldCallback = $callback;
        } else {
            $this->label = null;
        }

        return $this;
    }

    public function fieldAttributes($attributes)
    {
        $this->fieldAttributes = $attributes;

        return $this;
    }

    public function getFieldAttributes()
    {
        return $this->fieldAttributes;
    }

    public function hint($text, $class = null)
    {
        $this->hint[] = new Hint($text, $class);

        return $this;
    }

    // Form control methods for compatibility
    public function required($required = true)
    {
        if ($required) {
            $this->setAttribute('required', 'required');
        } else {
            $this->removeAttribute('required');
        }

        return $this;
    }

    public function optional()
    {
        $this->removeAttribute('required');

        return $this;
    }

    public function readonly($readonly = true)
    {
        if ($readonly) {
            $this->setAttribute('readonly', 'readonly');
        } else {
            $this->removeAttribute('readonly');
        }

        return $this;
    }

    public function disable($disable = true)
    {
        if ($disable) {
            $this->setAttribute('disabled', 'disabled');
        } else {
            $this->removeAttribute('disabled');
        }

        return $this;
    }

    public function enable($enable = true)
    {
        $this->disable(! $enable);

        return $this;
    }

    public function autofocus()
    {
        $this->setAttribute('autofocus', 'autofocus');

        return $this;
    }

    public function unfocus()
    {
        $this->removeAttribute('autofocus');

        return $this;
    }

    public function getValue()
    {
        return $this->getAttribute('value');
    }

    public function display()
    {
        return sprintf(
            '<tr %s><td style="width:300px"><div title="%s">%s</div></td><td>%s</td></tr>',
            $this->renderFieldAttributes(),
            $this->getAttribute('name'),
            $this->label,
            $this->displayValue()
        );
    }

    public function displayValue()
    {
        return nl2br($this->getValue() ?? '');
    }

    public function populateValue($values)
    {
        $name = $this->getAttribute('name');
        if ($name && isset($values[$name])) {
            if (method_exists($this, 'value')) {
                $this->value($values[$name]);
            } else {
                $this->setAttribute('value', $values[$name]);
            }
        }

        return $this;
    }

    public function normalizedName()
    {
        $name = $this->getAttribute('name');

        return trim(str_replace(']', '', str_replace('[', '.', $name)), '.');
    }

    public function basename()
    {
        $normalizedName = $this->normalizedName();
        $parts = explode('.', $normalizedName);

        return $parts[0] ?? '';
    }

    abstract public function render();

    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    protected function beforeRender()
    {
        return true;
    }

    protected function renderAttributes()
    {
        return form_html_attributes($this->attributes ?? []);
    }

    protected function renderFieldAttributes()
    {
        return form_html_attributes($this->fieldAttributes ?? []);
    }

    protected function renderHint()
    {
        $output = '';
        foreach ($this->hint as $hint) {
            if ($hint instanceof Hint) {
                $output .= $hint->render();
            }
        }

        return $output;
    }

    protected function renderLabel()
    {
        if ($this->label) {
            return $this->label->render();
        }

        return '';
    }

    protected function renderField()
    {
        $output = '<div class="space-y-1">';
        $output .= $this->renderLabel();
        $output .= $this->renderControl();
        $output .= $this->renderError();
        $output .= $this->renderHint();
        $output .= '</div>';

        return $output;
    }

    protected function renderError()
    {
        if ($this->hasError()) {
            return '<p class="text-sm text-red-600 mt-1">'.$this->getError().'</p>';
        }

        return '';
    }

    protected function hasError()
    {
        return false; // Override in form elements that support errors
    }

    protected function getError()
    {
        return '';
    }

    protected function renderControl()
    {
        return '';
    }
}
