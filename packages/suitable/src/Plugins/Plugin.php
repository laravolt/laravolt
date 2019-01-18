<?php

namespace Laravolt\Suitable\Plugins;

abstract class Plugin
{
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
}
