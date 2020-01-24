<?php

namespace Laravolt\SemanticForm\Elements;

class DatepickerWrapper extends InputWrapper
{
    protected $format;

    protected $withTime = false;

    public function format(string $format)
    {
        $this->format = $format;

        return $this;
    }

    public function withTime(bool $withTime = true)
    {
        $this->withTime = $withTime;

        return $this;
    }

    protected function beforeRender()
    {
        parent::beforeRender();

        $this->data('calendar-type', $this->withTime ? 'datetime' : 'date')
            ->data('calendar-format', $this->format);
    }
}
