<?php

namespace Laravolt\PrelineForm\Elements;

class SelectDateTimeWrapper extends Element
{
    protected $date;

    protected $month;

    protected $year;

    protected $time;

    public function __construct($date, $month, $year, $time)
    {
        $this->date = $date;
        $this->month = $month;
        $this->year = $year;
        $this->time = $time;
    }

    public function render()
    {
        $result = '<div class="grid grid-cols-4 gap-2">';
        $result .= $this->date->render();
        $result .= $this->month->render();
        $result .= $this->year->render();
        $result .= $this->time->render();
        $result .= '</div>';

        return $result;
    }

    public function value($value)
    {
        if ($value instanceof \Carbon\Carbon) {
            // Set individual components
            $this->date->element->value($value->day);
            $this->month->element->value($value->month);
            $this->year->element->value($value->year);
            $this->time->element->value($value->format('H:i'));
        }

        return $this;
    }
}
