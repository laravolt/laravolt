<?php

namespace Laravolt\SemanticForm\Elements;

class ActionWrapper extends Wrapper
{
    protected $attributes = ['class' => 'action pushed'];

    public function __construct($actions)
    {
        $this->controls = $actions;
    }
}
