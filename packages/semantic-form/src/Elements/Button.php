<?php namespace Laravolt\SemanticForm\Elements;

class Button extends FormControl
{

    protected $attributes = array(
        'type'  => 'button',
        'class' => 'ui button',
    );

    protected $value;

    public function __construct($value, $name = null)
    {
        parent::__construct($name);
        $this->value($value);
    }

    public function render()
    {
        $result = '<button';
        $result .= $this->renderAttributes();
        $result .= '>';
        $result .= $this->value;
        $result .= '</button>';

        return $result;
    }

    public function value($value)
    {
        $this->value = $value;
    }
}
