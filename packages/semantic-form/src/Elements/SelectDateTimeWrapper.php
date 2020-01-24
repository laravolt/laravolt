<?php

namespace Laravolt\SemanticForm\Elements;

class SelectDateTimeWrapper extends SelectDateWrapper
{
    protected $format = 'Y-m-d H:i:s';

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
        $this->getControl(3)->getControl(0)->select($date->format('H:i'));

        $this->value = $value;

        return $this;
    }
}
