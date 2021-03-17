<?php

namespace Laravolt\SemanticForm\Elements;

class Rupiah extends InputWrapper
{
    private string $prefix = 'Rp';

    public function __construct($input)
    {
        parent::__construct($input);

        $this->prependLabel($this->prefix);
        $this->getPrimaryControl()->data('role', 'rupiah');
    }

    public function displayValue()
    {
        return $this->prefix.number_format($this->getPrimaryControl()->displayValue(), 0, ',', '.');
    }
}
