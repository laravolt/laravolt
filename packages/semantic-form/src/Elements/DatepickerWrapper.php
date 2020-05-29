<?php

namespace Laravolt\SemanticForm\Elements;

class DatepickerWrapper extends InputWrapper
{
    protected $format;
    
    protected $type = 'date';

    public function format(string $format)
    {
        $this->format = $format;

        return $this;
    }

    public function withType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    protected function beforeRender()
    {
        parent::beforeRender();

        $this->data('calendar-type', $this->type)
            ->data('calendar-format', $this->format);
    }
}
