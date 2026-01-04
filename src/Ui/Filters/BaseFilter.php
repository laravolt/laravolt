<?php

declare(strict_types=1);

namespace Laravolt\Ui\Filters;

use Illuminate\Support\Str;
use ReflectionClass;

abstract class BaseFilter
{
    protected string $label = '';

    protected ?string $placeholder = null;

    abstract public function render(): string;

    public function key(): string
    {
        return Str::kebab((new ReflectionClass($this))->getShortName());
    }
}
