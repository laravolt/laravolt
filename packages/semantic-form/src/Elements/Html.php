<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

class Html extends Element
{
    protected $content;

    /**
     * Html constructor.
     */
    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function name() {}

    public function render()
    {
        if ($this->label) {
            $element = clone $this;
            $element->label = false;

            return $this->decorateField(new Field($this->label, $element))->addClass($this->fieldWidth)->render();
        }

        $this->beforeRender();

        $result = '<div style="margin-top: .5em"';
        $result .= $this->renderAttributes();
        $result .= ">$this->content</div>";
        $result .= $this->renderHint();

        return $result;
    }

    public function display()
    {
        return sprintf(
            '<tr %s><td><div title="%s">%s</div></td><td>%s</td></tr>',
            $this->renderFieldAttributes(),
            $this->getAttribute('name'),
            $this->label,
            $this->displayValue()
        );
    }

    public function displayValue()
    {
        return $this->content;
    }
}
