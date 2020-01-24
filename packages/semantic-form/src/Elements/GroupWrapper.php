<?php

namespace Laravolt\SemanticForm\Elements;

class GroupWrapper
{
    protected $formGroup;

    public function __construct($formGroup)
    {
        $this->formGroup = $formGroup;
    }

    public function render()
    {
        return $this->formGroup->render();
    }

    public function __toString()
    {
        return $this->render();
    }

    public function labelClass($class)
    {
        $this->formGroup->label()->addClass($class);

        return $this;
    }

    public function hideLabel()
    {
        $this->labelClass('sr-only');

        return $this;
    }

    public function inline()
    {
        $this->formGroup->inline();

        return $this;
    }

    public function __call($method, $parameters)
    {
        call_user_func_array([$this->formGroup->control(), $method], $parameters);

        return $this;
    }
}
