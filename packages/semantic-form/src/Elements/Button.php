<?php

namespace Laravolt\SemanticForm\Elements;

class Button extends FormControl
{
    protected $attributes = [
        'type'  => 'button',
        'class' => 'ui button',
    ];

    protected $text;

    public function __construct($text, $name)
    {
        parent::__construct($name);
        $this->text($text);
    }

    public function render()
    {
        if ($this->label) {
            $element = clone $this;
            $element->label = false;

            return $this->decorateField(new Field($this->label, $element))->addClass($this->fieldWidth)->render();
        }

        $result = '<button';
        $result .= $this->renderAttributes();
        $result .= '>';
        $result .= $this->text;
        $result .= '</button>';

        return $result;
    }

    public function text($text)
    {
        $this->text = $text;

        return $this;
    }

    public function value($value)
    {
        $this->setAttribute('value', $value);

        return $this;
    }
}
