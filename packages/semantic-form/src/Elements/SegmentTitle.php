<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

class SegmentTitle extends Wrapper
{
    protected $openTag = '<h3 %s>';

    protected $closeTag = '</h3>';

    protected $attributes = [
        'class' => 'ui header',
    ];

    public function display()
    {
        $output = '';
        foreach ($this->controls as $control) {
            $output .= "<h2 class='ui header horizontal divider section'>$control</h2>";
        }

        return $output;
    }
}
