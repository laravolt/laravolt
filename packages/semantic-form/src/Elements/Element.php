<?php namespace Laravolt\SemanticForm\Elements;

abstract class Element
{
    protected $attributes = array();

    protected $label = false;

    protected $fieldWidth;

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
        'fiveteen',
        'sizteen'
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

    public function getAttribute($attribute)
    {
        return $this->attributes[$attribute];
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

    public function label($label)
    {
        $this->label = new Label($label);

        return $this;
    }

    public function fieldWidth($width)
    {
        $this->getPrimaryControl()->fieldWidth = $this->normalizeWidth($width);

        return $this;
    }

    public function hint($text, $class = null)
    {
        $this->hint = new Hint($text, $class);

        return $this;
    }

    abstract public function render();

    public function __toString()
    {
        return $this->render();
    }

    protected function beforeRender()
    {
        return true;
    }

    protected function renderAttributes()
    {
        $result = '';
        foreach ($this->attributes as $attribute => $value) {
            $result .= " {$attribute}=\"{$value}\"";
        }

        return $result;
    }

    protected function renderHint()
    {
        $output = "";
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

    public function __call($method, $params)
    {
        $params = count($params) ? $params : array($method);
        $params = array_merge(array($method), $params);
        call_user_func_array(array($this, 'attribute'), $params);

        return $this;
    }
}
