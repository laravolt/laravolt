<?php

namespace Laravolt\Suitable\Concerns;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait SourceResolver
{
    public function resolve($source)
    {
        if ($source instanceof \Illuminate\Database\Eloquent\Builder) {
            return $source->get();
        } elseif (is_subclass_of($source, Model::class)) {
            return (new $source())->all();
        } elseif (is_string($source) && Schema::hasTable($source)) {
            return DB::table($source)->get();
        } elseif ($source instanceof Paginator) {
            return $source;
        } elseif ($source instanceof Collection) {
            return $source;
        } elseif ($source instanceof \Illuminate\Support\Collection) {
            return $source;
        } elseif (is_array($source)) {
            return collect($source);
        }

        $type = gettype($source);
        if (is_object($source)) {
            $type = get_class($source);
        }

        throw new \InvalidArgumentException('Cannot generate table from '.$type);
    }
}
