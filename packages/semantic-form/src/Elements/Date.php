<?php namespace Laravolt\SemanticForm\Elements;

use Carbon\Carbon;

class Date extends Text
{
    protected static $inputFormat = 'Y-m-d';

    protected static $displayFormat = 'd-m-Y';

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
        return Carbon::createFromFormat(static::$inputFormat, $this->getValue())->format(static::$displayFormat);
    }
}
