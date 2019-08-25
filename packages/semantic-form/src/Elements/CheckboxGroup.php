<?php
namespace Laravolt\SemanticForm\Elements;

class CheckboxGroup extends Wrapper
{

    protected $attributes = [
        'class' => 'grouped fields'
    ];

    protected $controls = [];

    public function inline()
    {
        $this->setAttribute('class', 'inline fields');

        return $this;
    }
}
