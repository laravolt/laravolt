<?php

namespace Laravolt\SemanticForm\Elements;

use Carbon\Carbon;

class Date extends Text
{
    protected static $inputFormat = 'YYYY-MM-DD';

    protected static $displayFormat = 'LL';

    protected $attributes = [
        'type' => 'date',
    ];

    public static function setInputFormat($format)
    {
        static::$inputFormat = $format;
    }

    public static function setDisplayFormat($format)
    {
        static::$displayFormat = $format;
    }

    public function value($value)
    {
        if ($value instanceof \DateTime) {
            $value = $value->format('Y-m-d');
        }

        return parent::value($value);
    }

    public function displayValue()
    {
        $value = $this->getValue();

        try {
            return Carbon::createFromIsoFormat(static::$inputFormat, $value)->isoFormat(static::$displayFormat);
        } catch (\Exception $e) {
            return $value;
        }
    }
}
