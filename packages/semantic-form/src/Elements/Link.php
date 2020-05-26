<?php

namespace Laravolt\SemanticForm\Elements;

use Illuminate\Support\Stringable;

class Link extends Element
{
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
        $colors = collect(config('laravolt.ui.colors'))->keys();
        $types = (new Stringable($this->attributes['class']))->explode(' ');

        if ($types->intersect($colors)->isEmpty()) {
            $this->addClass(config('laravolt.ui.color'));
        }

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
