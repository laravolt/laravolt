<?php

namespace Laravolt\SemanticForm\Elements;

use Carbon\Carbon;
use DateTime;

class SelectDateWrapper extends Wrapper
{
    protected $attributes = [
        'class' => 'inline fields',
    ];

    protected $value = null;

    protected $format = 'Y-m-d';

    public function value($value)
    {
        try {
            $date = $this->asDateTime($value);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(
                'Argument must be an instance of Carbon or DateTime, or date string in Y-m-d format.'
            );
        }

        $this->getControl(0)->getControl(0)->select($date->day);
        $this->getControl(1)->getControl(0)->select($date->month);
        $this->getControl(2)->getControl(0)->select($date->year);

        $this->value = $value;

        return $this;
    }

    public function defaultValue($value)
    {
        if (!$this->hasValue()) {
            return $this->value($value);
        }

        return $this;
    }

    /**
     * @param $value
     *
     * @return bool|Carbon
     */
    protected function asDateTime($value)
    {
        // If this value is already a Carbon instance, we shall just return it as is.
        // This prevents us having to reinstantiate a Carbon instance when we know
        // it already is one, which wouldn't be fulfilled by the DateTime check.
        if ($value instanceof Carbon) {
            return $value;
        }

        // If the value is already a DateTime instance, we will just skip the rest of
        // these checks since they will be a waste of time, and hinder performance
        // when checking the field. We will just return the DateTime right away.
        if ($value instanceof DateTime) {
            return Carbon::instance($value);
        }

        // If this value is an integer, we will assume it is a UNIX timestamp's value
        // and format a Carbon object from this timestamp. This allows flexibility
        // when defining your date fields as they might be UNIX timestamps here.
        if (is_numeric($value)) {
            return Carbon::createFromTimestamp($value);
        }

        return Carbon::createFromFormat($this->format, $value);
    }

    protected function hasValue()
    {
        return $this->value !== null;
    }
}
