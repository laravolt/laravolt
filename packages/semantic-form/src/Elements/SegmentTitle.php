<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

class SegmentTitle extends Wrapper
{
    public static $template = "<h3 class='ui blue ribbon label'>%s</h3>";

    protected $openTag = '<h3 %s>';

    protected $closeTag = '</h3>';

    protected $attributes = [
        'class' => 'ui header',
    ];

    public function display()
    {
        $output = '';
        foreach ($this->controls as $control) {
            $output .= sprintf(static::$template, $control);
        }

        return $output;
    }
}
