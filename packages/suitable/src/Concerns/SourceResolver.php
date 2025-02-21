<?php

namespace Laravolt\Suitable\Concerns;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait SourceResolver
{
    public function resolve($source)
    {
        if ($source instanceof Builder || $source instanceof \Illuminate\Database\Query\Builder) {
            return $source->paginate(request('per_page', $this->perPage));
        } elseif (is_subclass_of($source, Model::class)) {
            return (new $source)->all();
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
        } elseif ($source instanceof Response) {
            return $source->collect();
        }

        $type = gettype($source);
        if (is_object($source)) {
            $type = get_class($source);
        }

        throw new \InvalidArgumentException('Cannot generate table from '.$type);
    }
}
