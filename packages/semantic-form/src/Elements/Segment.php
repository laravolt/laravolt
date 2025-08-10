<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

class Segment extends Wrapper
{
    protected $attributes = [
        'class' => 'rounded-2xl border border-gray-200 bg-white p-4 dark:bg-neutral-800 dark:border-neutral-700',
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
