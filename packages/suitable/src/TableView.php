<?php

namespace Laravolt\Suitable;

use Illuminate\Contracts\Support\Responsable;
use Laravolt\Suitable\Contracts\Plugin;
use Laravolt\Suitable\Segments\Segment;
use Laravolt\Suitable\Toolbars\Search;
use Laravolt\Suitable\Toolbars\Title;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

abstract class TableView implements Responsable
{
    protected $source = null;

    protected $view = '';

    protected $data = [];

    protected $alias = 'table';

    protected $perPage = null;

    protected $plugins = [];

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
                    'print' => function () {
                        $view = $this->view ?: 'suitable::layouts.print';
                        $this->data = array_add($this->data, $this->alias, $this->table('print'));

                        return response()->view($view, $this->data);
                    },
                    'pdf' => function () {
                        return Pdf
                            ::loadView('suitable::layouts.pdf', [$this->alias => $this->table('pdf')])
                            ->stream('test.pdf');
                    },
                    'csv' => function () {
                        return fastexcel($this->buildSource('csv'))->configureCsv(';', '#', '\n', 'gbk')->download('test.csv');
                    },
                    'xls' => function () {
                        return fastexcel($this->buildSource('xls'))->download('test.xls');
                    },
                    'xlsx' => function () {
                        return fastexcel($this->buildSource('xlsx'))->download('test.xlsx');
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
        $table = app('laravolt.suitable');

        $table->segments(
            [
                Segment::make('first')
                    ->left(Title::make('Pengguna Aktif'))
                    ->right(Search::make('search')),
            ]
        );

        foreach ($this->plugins as $plugin)
        {
            if ($plugin instanceof Plugin) {
                $table = $plugin->decorate($table);
            }
        }

        return $table->format($format)
            ->source($this->buildSource())
            ->columns($this->columns())
            ->render();
    }

    protected function buildSource()
    {
        $source = $this->source;

        foreach ($this->plugins as $plugin)
        {
            if ($plugin instanceof Plugin) {
                $source = $plugin->resolve($source);
            }
        }

        return $source;
    }

    public function plugins($plugins)
    {
        $this->plugins = is_array($plugins) ? $plugins : func_get_args();

        return $this;
    }

    abstract protected function columns();

    abstract protected function segments();
}
