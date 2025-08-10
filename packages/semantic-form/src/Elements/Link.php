<?php

namespace Laravolt\SemanticForm\Elements;

use Laravolt\SemanticForm\Traits\Themeable;

class Link extends Element
{
    use Themeable;

    protected $attributes = [
        'class' => 'inline-flex items-center gap-x-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 hover:bg-gray-50 focus:outline-hidden focus:ring-2 focus:ring-gray-200 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700',
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
