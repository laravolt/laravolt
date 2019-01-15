<?php

namespace Laravolt\Suitable;

use Illuminate\Contracts\Support\Responsable;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

abstract class TableView implements Responsable
{
    protected $source = null;

    protected $view = '';

    protected $data = [];

    protected $alias = 'table';

    /**
     * TableView constructor.
     */
    public function __construct($source)
    {
        $this->source = $source;
    }

    public function toResponse($request)
    {
        return request()
            ->match(
                [
                    'html' => function () {
                        $view = $this->view ?: 'suitable::layouts.print';
                        $this->data = array_add($this->data, $this->alias, $this->table('html'));

                        return response()->view($view, $this->data);
                    },
                    'pdf' => function () {
                        return Pdf
                            ::loadView('suitable::layouts.pdf', [$this->alias => $this->table('pdf')])
                            ->stream('test.pdf');
                    },
                    'csv' => function () {
                        return fastexcel($this->source)->configureCsv(';', '#', '\n', 'gbk')->download('test.csv');
                    },
                    'xls' => function () {
                        return fastexcel($this->source)->download('test.xls');
                    },
                    'xlsx' => function () {
                        return fastexcel($this->source)->download('test.xlsx');
                    },
                ]
            );
    }

    public function view(string $view = '', array $data = [])
    {
        $this->view = $view;
        $this->data = $data;

        return $this;
    }

    public function alias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    protected function table($format)
    {
        return app('laravolt.suitable')
            ->format($format)
            ->source($this->source)
            ->columns($this->columns())
            ->render();
    }

    abstract protected function columns();
}
