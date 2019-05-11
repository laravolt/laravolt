<?php

namespace Laravolt\Suitable\Plugins;

use Laravolt\Suitable\Builder;
use Laravolt\Suitable\Toolbars\Search;

class Html extends Plugin implements \Laravolt\Suitable\Contracts\Plugin
{
    protected $view = '';

    protected $data = [];

    protected $alias = 'table';

    protected $perPage = null;

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

    public function paginate($perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function shouldResponse(): bool
    {
        return true;
    }

    public function decorate(Builder $table): Builder {
        $table->getDefaultSegment()->right(Search::make('search'));

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
        $this->data = array_add($this->data, $this->alias, $output);

        return response()->view($view, $this->data);
    }
}
