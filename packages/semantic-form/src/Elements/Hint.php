<?php

namespace Laravolt\SemanticForm\Elements;

class Hint extends Element
{
    public static $defaultClass = 'hint';

    protected $text;

    /**
     * Icon constructor.
     *
     * @param string icon name
     */
    public function __construct($text, $cssClass)
    {
        $this->text = $text;

        if (!$cssClass) {
            $cssClass = self::$defaultClass;
        }

        $this->addClass($cssClass);
    }

    public function render()
    {
        $html = '<div';
        $html .= $this->renderAttributes();
        $html .= '>';

        $html .= $this->text;

        $html .= '</div>';

        return $html;
    }
}
