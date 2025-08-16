<?php

namespace Laravolt\PrelineForm\Elements;

class Hidden extends Element
{
    protected $name;
    protected $value;

    public function __construct($name)
    {
        $this->name = $name;
        $this->setAttribute('type', 'hidden');
        $this->setAttribute('name', $name);
    }

    public function value($value)
    {
        $this->setAttribute('value', $value);

        return $this;
    }

    public function defaultValue($value)
    {
        if (is_null($this->getAttribute('value'))) {
            $this->value($value);
        }

        return $this;
    }

    public function render()
    {
        return sprintf('<input%s>', $this->renderAttributes());
    }
}