<?php

namespace Laravolt\SemanticForm\Elements;

use Laravolt\SemanticForm\Traits\Themeable;

class Button extends FormControl
{
    use Themeable;

    protected $attributes = [
        'type' => 'button',
        'class' => 'inline-flex items-center gap-x-2 rounded-lg border border-transparent bg-gray-800 text-white px-3 py-2 text-sm hover:bg-gray-700 focus:outline-hidden focus:ring-2 focus:ring-gray-600 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600',
        'themed',
    ];

    protected $text;

    public function __construct($text, $name)
    {
        parent::__construct($name);
        $this->text($text);
    }

    public function render()
    {
        $this->applyTheme();

        if ($this->label) {
            $element = clone $this;
            $element->label = false;

            return $this->decorateField(new Field($this->label, $element))
                ->addClass($this->fieldWidth)
                ->render();
        }

        $result = '<button';
        $result .= $this->renderAttributes();
        $result .= '>';
        $result .= $this->text;
        $result .= '</button>';

        return $result;
    }

    public function text($text)
    {
        $this->text = $text;

        return $this;
    }

    public function value($value)
    {
        $this->setAttribute('value', $value);

        return $this;
    }
}
