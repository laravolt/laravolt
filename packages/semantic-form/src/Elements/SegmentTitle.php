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
}
