<?php namespace Laravolt\SemanticForm\Elements;

class Button extends FormControl
{

    protected $attributes = [
        'type'  => 'button',
        'class' => 'ui button',
    ];

    protected $label;

    public function __construct($label, $name)
    {
        parent::__construct($name);
        $this->label($label);
    }

    public function render()
    {
        $result = '<button';
        $result .= $this->renderAttributes();
        $result .= '>';
        $result .= $this->label;
        $result .= '</button>';

        return $result;
    }

    public function label($label)
    {
        $this->label = $label;

        return $this;
    }

    public function value($value)
    {
        $this->setAttribute('value', $value);

        return $this;
    }
}
