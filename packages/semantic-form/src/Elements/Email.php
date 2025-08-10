<?php

namespace Laravolt\SemanticForm\Elements;

class Email extends Text
{
    public function __construct($name)
    {
        parent::__construct($name);
        $this->attribute('type', 'email');
    }
}
