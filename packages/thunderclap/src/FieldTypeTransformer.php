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
            case StringType::class:
                return $this->text();
                break;
            case TextType::class:
                return $this->textarea();
                break;
            case DateType::class:
                return $this->date();
                break;
            case DateTimeType::class:
                return $this->datetime();
                break;
            default:
                return $this->text();
                break;
        }
    }

    private function text()
    {
        return "{!! form()->text('%s')->label('%s') !!}";
    }

    private function textarea()
    {
        return "{!! form()->textarea('%s')->label('%s') !!}";
    }

    private function date()
    {
        return "{!! form()->selectDate('%s')->label('%s') !!}";
    }

    private function datetime()
    {
        return "{!! form()->datepicker('%s')->label('%s') !!}";
    }
}
