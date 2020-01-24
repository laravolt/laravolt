<?php

namespace Laravolt\Suitable\Plugins;

use Laravolt\Suitable\Builder;

class Json extends Html implements \Laravolt\Suitable\Contracts\Plugin
{
    protected $shouldResponse = false;

    public function init()
    {
        $this->shouldResponse = request()->wantsJson() || request('format') == 'json';
    }

    public function shouldResponse(): bool
    {
        return $this->shouldResponse;
    }

    public function decorate(Builder $table): Builder
    {
        return $table;
    }

    public function resolve($source)
    {
        if ($source instanceof \Illuminate\Database\Eloquent\Builder) {
            return $source->paginate();
        }

        return $source;
    }

    public function response($source, Builder $table)
    {
        $source = $this->resolve($source);

        return response()->json($source);
    }
}
