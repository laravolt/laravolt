<?php

declare(strict_types=1);

namespace Laravolt\Suitable\Concerns;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

trait SourceResolver
{
    public function resolve($source)
    {
        if ($source instanceof Builder || $source instanceof \Illuminate\Database\Query\Builder) {
            return $source->paginate(request('per_page', $this->perPage));
        }
        if (is_subclass_of($source, Model::class)) {
            return (new $source)->all();
        }
        if (is_string($source) && Schema::hasTable($source)) {
            return DB::table($source)->get();
        }
        if ($source instanceof Paginator) {
            return $source;
        }
        if ($source instanceof Collection) {
            return $source;
        }
        if ($source instanceof \Illuminate\Support\Collection) {
            return $source;
        }
        if (is_array($source)) {
            return collect($source);
        }
        if ($source instanceof Response) {
            return $source->collect();
        }

        $type = gettype($source);
        if (is_object($source)) {
            $type = get_class($source);
        }

        throw new InvalidArgumentException('Cannot generate table from '.$type);
    }
}
