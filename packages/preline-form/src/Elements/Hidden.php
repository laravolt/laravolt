<?php

declare(strict_types=1);

namespace Laravolt\PrelineForm\Elements;

class Hidden extends Input
{
    protected $attributes = [
        'type' => 'hidden',
    ];

    public function __construct($name)
    {
        parent::__construct($name);
    }

    public function value($value)
    {
        $this->setValue($value);

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
        $this->beforeRender();

        $result = '<input';
        $result .= $this->renderAttributes();
        $result .= '>';

        return $result;
    }
}
