<?php

namespace Laravolt\SemanticForm\Elements;

class Rupiah extends InputWrapper
{
    public function __construct($input)
    {
        parent::__construct($input);

        $this->prependLabel('Rp');
        $this->getPrimaryControl()->data('role', 'rupiah');
    }

    public function displayValue()
    {
        return 'Rp'.number_format($this->getPrimaryControl()->displayValue(), 0, ',', '.');
    }
}
