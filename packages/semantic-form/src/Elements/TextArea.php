<?php namespace Laravolt\SemanticForm\Elements;

class TextArea extends FormControl
{

    protected $attributes = array(
        'name' => '',
        'rows' => 10,
        'cols' => 50,
    );

    protected $value;

    public function render()
    {
        if ($this->label) {
            $element = clone $this;
            $element->label = false;
            return (new Field($this->label, $element))->render();
        }

        $result = '<textarea';
        $result .= $this->renderAttributes();
        $result .= '>';
        $result .= $this->value;
        $result .= '</textarea>';
        $result .= $this->renderHint();

        return $result;
    }

    public function rows($rows)
    {
        $this->setAttribute('rows', $rows);
        return $this;
    }

    public function cols($cols)
    {
        $this->setAttribute('cols', $cols);
        return $this;
    }

    public function value($value)
    {
        $this->value = $value;
        return $this;
    }

    public function placeholder($placeholder)
    {
        $this->setAttribute('placeholder', $placeholder);
        return $this;
    }

    public function defaultValue($value)
    {
        if (! $this->hasValue()) {
            $this->value($value);
        }

        return $this;
    }

    protected function hasValue()
    {
        return isset($this->value);
    }
}
