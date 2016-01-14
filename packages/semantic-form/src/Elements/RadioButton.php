<?php namespace Laravolt\SemanticForm\Elements;

class RadioButton extends Checkbox
{
    protected $attributes = array(
        'type' => 'radio',
    );

    public function __construct($name, $value = null)
    {
        parent::__construct($name);

        if (is_null($value)) {
            $value = $name;
        }

        $this->setValue($value);
    }

    public function render()
    {
        if ($this->label) {
            $element = clone $this;
            $element->label = false;

            return (new Field(new RadioButtonWrapper($element, $this->label)))->render();
        }

        $result = '<input';

        $result .= $this->renderAttributes();

        $result .= '>';

        return $result;
    }
}
