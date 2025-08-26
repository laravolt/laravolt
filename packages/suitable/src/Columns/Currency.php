<?php

namespace Laravolt\Suitable\Columns;

use Illuminate\Support\Exceptions\MathException;

class Currency extends Column implements ColumnInterface
{
    protected string $prefix = '';

    protected string $suffix = '';

    protected int $decimals = 0;

    protected string $decimalSeparator = ',';

    protected string $thousandsSeparator = '.';

    public function cell($cell, $collection, $loop)
    {
        try {
            $value = data_get($cell, $this->field);
        } catch (MathException ) {
            return '-';
        }

        if ($value === null || $value === '') {
            return '-';
        }

        $formatted = number_format($value, $this->decimals, $this->decimalSeparator, $this->thousandsSeparator);

        return $this->prefix.$formatted.$this->suffix;
    }

    public function prefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function suffix(string $suffix): self
    {
        $this->suffix = $suffix;

        return $this;
    }

    public function decimals(int $decimals): self
    {
        $this->decimals = $decimals;

        return $this;
    }
}
