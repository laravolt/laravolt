<?php

namespace Laravolt\Suitable\Plugins;

use Laravolt\Suitable\Builder;
use Laravolt\Suitable\Concerns\SourceResolver;

abstract class Plugin
{
    use SourceResolver;

    protected $only = [];

    public function init()
    {
    }

    public function decorate(Builder $table): Builder
    {
        return $table;
    }

    public function filter($columns)
    {
        if (count($this->only) == 0) {
            return $columns;
        }

        return collect($columns)->filter(function ($item) {
            return in_array($item->id(), $this->only);
        })->toArray();
    }

    public function only($columns)
    {
        $this->only = is_array($columns) ? $columns : func_get_args();

        return $this;
    }
}
