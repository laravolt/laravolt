<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm\Elements;

class ActionWrapper extends Wrapper
{
    protected $attributes = ['class' => 'actions action pushed'];

    public function __construct($actions)
    {
        $this->controls = $actions;
    }
}
