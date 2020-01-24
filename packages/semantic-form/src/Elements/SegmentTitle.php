<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

class SegmentTitle extends Wrapper
{
    protected static $template = "<h3 class='ui blue ribbon label'>%s</h3>";

    public static function setTemplate(string $template)
    {
        static::$template = $template;
    }

    public function render()
    {
        $element = clone $this;

        if ($this->label) {
            $element->label = false;

            $field = $this->decorateField(new Field($this->label, $element));
            if ($control = $element->getPrimaryControl()) {
                $field->addClass($control->fieldWidth);
            }

            return $field->render();
        }

        $element->beforeRender();

        $html = '';
        foreach ($element->controls as $control) {
            $html .= sprintf(static::$template, $control);
        }

        $html .= $element->renderHint();

        return $html;
    }

    public function display()
    {
        $output = '';
        foreach ($this->controls as $control) {
            $output .= sprintf(static::$template, $control);
        }

        return $output;
    }
}
