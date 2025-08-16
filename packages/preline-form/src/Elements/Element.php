<?php

namespace Laravolt\PrelineForm\Elements;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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

    public function addClass($class)
    {
        $existingClasses = $this->getAttribute('class');
        if ($existingClasses) {
            $this->setAttribute('class', $existingClasses . ' ' . $class);
        } else {
            $this->setAttribute('class', $class);
        }

        return $this;
    }

    public function removeClass($class)
    {
        $existingClasses = $this->getAttribute('class');
        if ($existingClasses) {
            $classes = explode(' ', $existingClasses);
            $classes = array_diff($classes, [$class]);
            $this->setAttribute('class', implode(' ', $classes));
        }

        return $this;
    }

    public function attribute($attribute, $value = null)
    {
        $this->setAttribute($attribute, $value);

        return $this;
    }

    public function data($attribute, $value = null)
    {
        $this->setAttribute('data-' . $attribute, $value);

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
            return '<p class="text-sm text-red-600 mt-1">' . $this->getError() . '</p>';
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