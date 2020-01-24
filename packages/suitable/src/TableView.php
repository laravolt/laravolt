<?php

namespace Laravolt\Suitable;

use Illuminate\Contracts\Support\Responsable;
use Laravolt\Suitable\Plugins\Html;

abstract class TableView implements Responsable
{
    protected $source = null;

    protected $plugins = [];

    protected $search = null;

    protected $title = '';

    protected $html;

    protected $decorateCallback;

    /**
     * TableView constructor.
     */
    public function __construct($source)
    {
        $this->source = $source;
        $this->html = new Html();

        $this->init();
    }

    public static function make($source)
    {
        $table = new static($source);

        return $table;
    }

    public function toResponse($request)
    {
        if ($this->search !== null) {
            $this->html->search($this->search);
        }

        $source = $this->getSource();
        $table = app('laravolt.suitable')->source($this->html->resolve($source));

        if (is_string($this->title) && $this->title !== '') {
            $table->title($this->title);
        }

        // Start decorating table
        // 1. HTML decoration
        $this->html->decorate($table);

        // 2. User defined decoration
        if (is_callable($this->decorateCallback)) {
            call_user_func($this->decorateCallback, $table);
        }

        // 3. Plugin decoration
        collect($this->plugins)->each->decorate($table);

        foreach ($this->plugins as $plugin) {
            if ($plugin->shouldResponse()) {
                $table->columns($plugin->filter($this->columns()));

                return $plugin->response($source, $table);
            }
        }

        $table->columns($this->html->filter($this->columns()));

        return $this->html->response($source, $table);
    }

    public function view(string $view = '', array $data = [])
    {
        $this->html->view($view)->data($data);

        return $this;
    }

    public function alias($alias)
    {
        $this->html->alias($alias);

        return $this;
    }

    public function paginate($perPage)
    {
        $this->html->paginate($perPage);

        return $this;
    }

    public function plugins($plugins)
    {
        $this->plugins = is_array($plugins) ? $plugins : func_get_args();

        collect($this->plugins)->each->init();

        return $this;
    }

    public function title(string $title)
    {
        $this->title = $title;

        return $this;
    }

    public function search($search)
    {
        $this->search = $search;

        return $this;
    }

    public function decorate(\Closure $callback)
    {
        $this->decorateCallback = $callback;

        return $this;
    }

    protected function getSource()
    {
        if (!$this->source && method_exists($this, 'source')) {
            return $this->source();
        }

        return $this->source;
    }

    protected function init()
    {
    }

    protected function segments()
    {
    }

    abstract protected function columns();
}
