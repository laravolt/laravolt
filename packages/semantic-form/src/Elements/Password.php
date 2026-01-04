<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

class Password extends Text
{
    protected $attributes = [
        'type' => 'password',
    ];
}
