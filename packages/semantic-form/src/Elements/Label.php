<?php

namespace Laravolt\SemanticForm\Elements;

class Label extends Element
{
    private $element;

    private $labelBefore;

    private $text;

    private $idAttribute;

    public function __construct($text, $idAttribute = null)
    {
        $this->text = $text;
        $this->idAttribute = $idAttribute ?: bin2hex(random_bytes(4)).'-'.$text;
    }

    public function render()
    {
        // Apply Preline/Tailwind default label classes if not provided
        $defaultClasses = 'block text-sm mb-2 dark:text-white';
        $existing = $this->getAttribute('class');

        $this->addClass(trim(($existing ? $existing.' ' : '').$defaultClasses));
        $this->attribute('for', $this->getAttribute('for'));

        $result = '<label';
        $result .= $this->renderAttributes();
        $result .= '>';

        if ($this->labelBefore) {
            $result .= $this->text;
        }

        $result .= $this->renderElement();

        if (! $this->labelBefore) {
            $result .= $this->text;
        }

        $result .= '</label>';

        return $result;
    }

    public function forId($name)
    {
        $this->setAttribute('for', $name);

        return $this;
    }

    public function before(Element $element)
    {
        $this->element = $element;
        $this->labelBefore = true;

        return $this;
    }

    public function after(Element $element)
    {
        $this->element = $element;
        $this->labelBefore = false;

        return $this;
    }

    protected function renderElement()
    {
        if (! $this->element) {
            return '';
        }

        return $this->element->render();
    }

    public function getControl()
    {
        return $this->element;
    }
}
