<?php

declare(strict_types=1);

namespace Laravolt\PrelineForm\Elements;

class Password extends Text
{
    public function __construct($name)
    {
        parent::__construct($name);
        $this->setAttribute('type', 'password');
        // Remove value attribute for password fields
        unset($this->attributes['value']);
    }

    public function value($value)
    {
        // Password fields should not retain values for security
        return $this;
    }
}
