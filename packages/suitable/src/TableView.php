<?php

namespace Laravolt\Suitable;

use Illuminate\Contracts\Support\Responsable;

abstract class TableView implements Responsable
{
    protected $source = null;

    protected $view = '';

    protected $data = [];

    /**
     * TableView constructor.
     * @param string $view
     * @param array $data
     */
    public function __construct($source, string $view, array $data)
    {
        $this->source = $source;
        $this->view = $view;
        $this->data = $data;
    }

    public function toResponse($request)
    {
        if ($this->view) {
            $this->data = array_add($this->data, 'table', $this->table());

            return response()->view($this->view, $this->data);
        }
    }

    public function view($view, $data)
    {
        $this->view = $view;
        $this->data = $data;

        return $this;
    }

    abstract protected function table();
}
