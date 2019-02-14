<?php

namespace Laravolt\Suitable\Plugins;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
        } elseif (is_string($source) && Schema::hasTable($source)) {
            return DB::table($source)->get();
        } elseif($source instanceof LengthAwarePaginator) {
            return $source;
        }

        $type = gettype($source);
        if (is_object($source)) {
            $type = get_class($source);
        }

        throw new \InvalidArgumentException('Cannot generate table from '.$type);
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
