<?php

namespace Laravolt\Thunderclap;

use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TextType;

class FieldTypeTransformer
{
    public function generate(array $column)
    {
        $class = get_class($column['type']);
        switch ($class) {
            case TextType::class:
                return $this->textType();
                break;
            case DateType::class:
                return $this->dateType();
                break;
            case DateTimeType::class:
                return $this->dateTimeType();
                break;
            default:
                return $this->stringType();
                break;
        }
    }

    private function stringType()
    {
        return "{!! form()->text('%s')->label('%s') !!}";
    }

    private function textType()
    {
        return "{!! form()->textarea('%s')->label('%s') !!}";
    }

    private function dateType()
    {
        return "{!! form()->selectDate('%s')->label('%s') !!}";
    }

    private function dateTimeType()
    {
        return "{!! form()->datepicker('%s')->label('%s') !!}";
    }
}
