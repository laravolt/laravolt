<?php

namespace Laravolt\Suitable\Plugins;

use Illuminate\Support\Arr;
use Laravolt\Suitable\Builder;
use Laravolt\Suitable\Toolbars\Search;

class Html extends Plugin implements \Laravolt\Suitable\Contracts\Plugin
{
    protected $view = '';

    protected $data = [];

    protected $alias = 'table';

    protected $search = null;

    protected $perPage = null;

    public function __construct()
    {
        $this->search = config('suitable.query_string.search');
    }

    public function view($view)
    {
        $this->view = $view;

        return $this;
    }

    public function data($data)
    {
        $this->data = $data;

        return $this;
    }

    public function alias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    public function search($search)
    {
        if (!is_bool($search) && !is_string($search)) {
            throw new \InvalidArgumentException('Only boolean or string allowed');
        }
        if ($search === true) {
            $this->search = config('suitable.query_string.search');
        } else {
            $this->search = $search;
        }

        return $this;
    }

    public function paginate($perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function shouldResponse(): bool
    {
        return true;
    }

    public function decorate(Builder $table): Builder
    {
        if ($this->search) {
            $table->getDefaultSegment()->right(Search::make($this->search));
        }

        return $table;
    }

    public function resolve($source)
    {
        if ($this->perPage && $source instanceof \Illuminate\Database\Eloquent\Builder) {
            return $source->paginate($this->perPage);
        }

        return parent::resolve($source);
    }

    public function response($source, Builder $table)
    {
        $output = $table->source($this->resolve($source))->render();
        $view = $this->view ?: 'suitable::layouts.print';
        $this->data = Arr::add($this->data, $this->alias, $output);

        return response()->view($view, $this->data);
    }
}
