<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

class Email extends Text
{
    protected $attributes = [
        'type' => 'email',
    ];
}
