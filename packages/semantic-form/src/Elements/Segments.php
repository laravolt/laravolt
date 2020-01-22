<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

class Segments extends Wrapper
{
    protected $attributes = [
        'class' => 'ui segments',
    ];

    public function __construct()
    {
        if (func_num_args() == 1 && is_array(func_get_arg(0))) {
            $controls = func_get_arg(0);
        } else {
            $controls = func_get_args();
        }

        $items = [];
        foreach ($controls as $control) {
            if (!$control instanceof Segment) {
                $control = new Segment($control);
            }
            $items[] = $control;
        }

        parent::__construct($items);
    }

    public function display()
    {
        $output = '';
        foreach ($this->controls as $control) {
            $output .= $control->display();
        }

        return $output;
    }
}
