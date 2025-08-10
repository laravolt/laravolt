<?php

namespace Laravolt\SemanticForm\Elements;

use Laravolt\SemanticForm\Traits\Themeable;

class Button extends FormControl
{
    use Themeable;

    protected $attributes = [
        'type' => 'button',
        'class' => 'inline-flex items-center justify-center gap-x-2 rounded-lg text-sm font-medium focus:outline-hidden transition-all disabled:opacity-50 disabled:pointer-events-none px-3.5 py-2.5 bg-blue-600 text-white hover:bg-blue-700 focus:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600',
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
        // Tailwind/Preline buttons don't use theme tokens; keep class-based variants set by caller if any

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
