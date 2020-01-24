<?php

declare(strict_types=1);

namespace Laravolt\Workflow\FieldFormatter\Db;

class DbFormatterFactory
{
    public static function make($key, $value, $type)
    {
        $type = ucfirst($type);
        $formatter = "\\Laravolt\\Workflow\\FieldFormatter\\Db\\{$type}Formatter";
        if (!class_exists($formatter)) {
            $formatter = StringFormatter::class;
        }
        $formatter = new $formatter();

        return $formatter($key, $value);
    }
}
