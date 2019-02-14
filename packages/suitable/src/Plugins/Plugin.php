<?php

namespace Laravolt\Suitable\Plugins;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravolt\Suitable\Builder;

abstract class Plugin
{
    protected $only = [];

    public function init()
    {
    }

    public function decorate(Builder $table): Builder {
        return $table;
    }

    public function resolve($source)
    {
        if ($source instanceof \Illuminate\Database\Eloquent\Builder) {
            return $source->get();
        } elseif (is_subclass_of($source, Model::class)) {
            return (new $source)->all();
        } elseif (Schema::hasTable($source)) {
            return DB::table($source)->get();
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
