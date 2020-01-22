<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

class Segment extends Wrapper
{
    protected $attributes = [
        'class' => 'ui segment',
    ];

    public function display()
    {
        $output = '';
        foreach ($this->controls as $control) {
            $output .= $control->display();
        }

        return $output;
    }
}
