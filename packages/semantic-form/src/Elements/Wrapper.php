<?php namespace Laravolt\SemanticForm\Elements;

use Illuminate\Support\Arr;

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

    public function getControl($key)
    {
        return Arr::get($this->controls, $key, null);
    }
}
