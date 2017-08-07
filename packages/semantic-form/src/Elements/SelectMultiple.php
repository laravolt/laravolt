<?php namespace Laravolt\SemanticForm\Elements;

class SelectMultiple extends Select
{

    private $selected;

    protected $attributes = [
        'class'    => 'ui dropdown search multiple tag',
        'multiple' => 'multiple',
    ];

    public function select($selected)
    {
        $selected = (array)$selected;
        $this->selected = $selected ?? [];
        $this->data('value', implode(',', $this->selected));

        return $this;
    }

    public function render()
    {
        if ($this->label) {
            $element = clone $this;
            $element->label = false;

            return (new Field($this->label, $element))->render();
        }

        $result = '<select';
        $result .= $this->renderAttributes();
        $result .= '>';
        $result .= $this->renderOptions();
        $result .= '</select>';
        $result .= $this->renderHint();

        return $result;
    }

}
