<?php

namespace Laravolt\SemanticForm\Elements;

use Laravolt\SemanticForm\Traits\Themeable;

class Link extends Element
{
    use Themeable;

    protected $attributes = [
        'class' => 'ui basic button',
        'themed',
    ];

    protected $text;

    protected $url;

    public function __construct($text, $url)
    {
        $this->text($text);
        $this->url($url);
    }

    public function render()
    {
        $this->applyTheme();

        if ($this->label) {
            $element = clone $this;
            $element->label = false;

            return $this->decorateField(new Field($this->label, $element))->addClass($this->fieldWidth)->render();
        }

        $result = '<a';
        $result .= $this->renderAttributes();
        $result .= '>';
        $result .= $this->text;
        $result .= '</a>';

        return $result;
    }

    public function text($text)
    {
        $this->text = $text;

        return $this;
    }

    public function url($url)
    {
        $this->setAttribute('href', $url);

        return $this;
    }
}
