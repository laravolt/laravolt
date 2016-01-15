<?php namespace Laravolt\SemanticForm\Elements;

class Wrapper extends Element
{
    protected $controls = [];
    protected $helpBlock;

    public function __construct()
    {
        $this->controls = func_get_args();
    }

    public function render()
    {

        if ($this->label) {
            $element = clone $this;
            $element->label = false;
            return (new Field($this->label, $element))->render();
        }

        $html = '<div';
        $html .= $this->renderAttributes();
        $html .= '>';

        foreach ($this->controls as $control) {
            $html .= $control;
        }

        $html .= $this->renderHelpBlock();

        $html .= '</div>';

        return $html;
    }

    public function helpBlock($text)
    {
        if (isset($this->helpBlock)) {
            return;
        }
        $this->helpBlock = new HelpBlock($text);

        return $this;
    }

    protected function renderHelpBlock()
    {
        if ($this->helpBlock) {
            return $this->helpBlock->render();
        }

        return '';
    }

    public function __call($method, $parameters)
    {
        call_user_func_array(array($this->control, $method), $parameters);

        return $this;
    }
}
