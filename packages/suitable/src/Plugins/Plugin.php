<?php

namespace Laravolt\Suitable\Plugins;

abstract class Plugin
{
    protected $only = [];

    protected $except = [];

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

    public function only($columns)
    {
        $this->only = is_array($columns) ? $columns : func_get_args();

        return $this;
    }

    public function except($columns)
    {
        $this->except = is_array($columns) ? $columns : func_get_args();

        return $this;
    }
}
