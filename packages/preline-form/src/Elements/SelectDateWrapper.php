<?php

namespace Laravolt\PrelineForm\Elements;

class SelectDateWrapper extends Element
{
    protected $date;

    protected $month;

    protected $year;

    public function __construct($date, $month, $year)
    {
        $this->date = $date;
        $this->month = $month;
        $this->year = $year;
    }

    public function render()
    {
        $result = '<div class="grid grid-cols-3 gap-2">';
        $result .= $this->date->render();
        $result .= $this->month->render();
        $result .= $this->year->render();
        $result .= '</div>';

        return $result;
    }
}
