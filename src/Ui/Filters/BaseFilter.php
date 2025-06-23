<?php

namespace Laravolt\Ui\Filters;

use Illuminate\Support\Str;

abstract class BaseFilter
{
    protected string $label = '';

    protected ?string $placeholder = null;

    abstract public function render(): string;

    public function key(): string
    {
        return Str::kebab((new \ReflectionClass($this))->getShortName());
    }
}
