<?php

namespace Laravolt\UiComponent\Filters;

use Illuminate\Support\Str;

abstract class BaseFilter
{
    protected string $label = '';

    abstract public function render(): string;

    public function key(): string
    {
        return Str::kebab((new \ReflectionClass($this))->getShortName());
    }

    protected function label(): string
    {
        return $this->label;
    }
}
