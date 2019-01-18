<?php

namespace Laravolt\Suitable\Plugins;

abstract class Plugin
{
    protected $only = [];

    public function init()
    {
    }

    public function resolve($source)
    {
        if ($source instanceof \Illuminate\Database\Eloquent\Builder) {
            return $source->get();
        }

        return $source;
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
