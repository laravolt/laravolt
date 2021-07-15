<?php

namespace Laravolt\SemanticForm\Elements;

use Carbon\Carbon;
use Illuminate\Support\Str;

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
            ->data('calendar-format', $this->withTime ? Str::of($this->format)->before(' ') : $this->format);
    }

    public function attributes($attributes)
    {
        foreach ($this->controls as $control) {
            if ($control instanceof Text) {
                $control->attributes($attributes);
            }
        }

        return $this;
    }

    public function displayValue()
    {
        $value = $this->getPrimaryControl()->getValue();

        try {
            return Carbon::createFromFormat($this->format, $value)->isoFormat($this->withTime ? 'LLL' : 'LL');
        } catch (\Exception $e) {
            return $value;
        }
    }
}
