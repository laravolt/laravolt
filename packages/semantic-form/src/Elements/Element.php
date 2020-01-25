<?php

namespace Laravolt\SemanticForm\Elements;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class Element
{
    protected $attributes = [];

    protected $fieldAttributes = [];

    protected $label = false;

    protected $fieldWidth;

    protected $fieldCallback = null;

    protected $hint = false;

    protected $widthTranslation = [
        1 => 'one',
        'two',
        'three',
        'four',
        'five',
        'six',
        'seven',
        'eight',
        'nine',
        'ten',
        'eleven',
        'twelve',
        'thirteen',
        'fourteen',
        'fifteen',
        'sixteen',
    ];

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

    protected function removeAttribute($attribute)
    {
        unset($this->attributes[$attribute]);
    }

    public function getAttribute($attribute, $default = null)
    {
        return $this->attributes[$attribute] ?? $default;
    }

    public function hasAttribute($attribute)
    {
        return isset($this->attributes[$attribute]);
    }

    public function data($attribute, $value)
    {
        $this->setAttribute('data-'.$attribute, $value);

        return $this;
    }

    public function attribute($attribute, $value)
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

    public function clear($attribute)
    {
        if (!isset($this->attributes[$attribute])) {
            return $this;
        }

        $this->removeAttribute($attribute);

        return $this;
    }

    public function addClass($class)
    {
        if (!$class) {
            return $this;
        }

        if (isset($this->attributes['class'])) {
            $existingClasses = explode(' ', $this->attributes['class']);
            $newClasses = explode(' ', $class);

            $class = implode(' ', array_unique(array_merge($existingClasses, $newClasses)));
        }

        $this->setAttribute('class', $class);

        return $this;
    }

    public function removeClass($class)
    {
        if (!isset($this->attributes['class'])) {
            return $this;
        }

        $class = trim(str_replace($class, '', $this->attributes['class']));
        if ($class == '') {
            $this->removeAttribute('class');

            return $this;
        }

        $this->setAttribute('class', $class);

        return $this;
    }

    public function id($id)
    {
        $this->setId($id);

        return $this;
    }

    protected function setId($id)
    {
        $this->setAttribute('id', $id);
    }

    public function label($label, \Closure $callback = null)
    {
        if ($label) {
            $this->label = new Label($label);
            $this->fieldCallback = $callback;
        } else {
            $this->label = null;
        }

        return $this;
    }

    public function fieldWidth($width)
    {
        $this->getPrimaryControl()->fieldWidth = $this->normalizeWidth($width);

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
        $this->hint = new Hint($text, $class);

        return $this;
    }

    abstract public function render();

    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            report($e);
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
        if ($this->hint instanceof Hint) {
            $output .= $this->hint->render();
        }

        return $output;
    }

    protected function normalizeWidth($width)
    {
        if (is_string($width)) {
            return $width.' wide';
        }

        if (is_numeric($width) && isset($this->widthTranslation[$width])) {
            return $this->widthTranslation[$width].' wide';
        }

        return null;
    }

    protected function decorateField(Field $field)
    {
        if ($this->fieldCallback instanceof \Closure) {
            call_user_func($this->fieldCallback, $field);
        }

        return $field;
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
        return $this->value ?? $this->getAttribute('value');
    }

    public function bindAttribute()
    {
        $args = func_get_args();
        $element = $this->getPrimaryControl();
        $attribute = $args[0] ?? null;
        if ($attribute) {
            unset($args[0]);
            $element->setAttribute($attribute, sprintf($element->getAttribute($attribute), ...$args));
        }

        return $this;
    }

    public function populateValue($values)
    {
        $element = $this->getPrimaryControl();

        return $element->value(Arr::get($values, $element->normalizedName()));
    }

    public function normalizedName()
    {
        $element = $this->getPrimaryControl();
        $name = $element->getAttribute('name');

        return trim(str_replace(']', '', str_replace('[', '.', $name)), '.');
    }

    public function basename()
    {
        return Str::before($this->normalizedName(), '.');
    }

    public function __call($method, $params)
    {
        $params = count($params) ? $params : [$method];
        $params = array_merge([$method], $params);
        call_user_func_array([$this, 'attribute'], $params);

        return $this;
    }
}
