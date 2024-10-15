<?php

namespace Laravolt\Suitable\Plugins;

use Laravolt\Suitable\Builder;
use Laravolt\Suitable\Columns\RestfulButton;
use Laravolt\Suitable\Concerns\SourceOverridden;
use Laravolt\Suitable\Toolbars\Action;

class Pdf extends Plugin implements \Laravolt\Suitable\Contracts\Plugin
{
    use SourceOverridden;

    protected $shouldResponse = false;

    protected $filename = 'test.pdf';

    protected $config = [];

    /**
     * Pdf constructor.
     */
    public function __construct(string $filename, array $config = [])
    {
        $this->filename = $filename;
        $this->config = $config;
    }

    public function init()
    {
        $this->shouldResponse = request('format') === 'pdf';
    }

    public function shouldResponse(): bool
    {
        return $this->shouldResponse;
    }

    public function decorate(Builder $table): Builder
    {
        $url = request()->url().'?'.http_build_query(array_merge(request()->input(), ['format' => 'pdf']));

        $segment = $table->getDefaultSegment();
        $segment->addLeft(Action::make('file pdf', 'Export To Pdf', $url));

        return $table;
    }

    public function response($source, Builder $table)
    {
        $columns = $table->getColumns();
        foreach ($columns as $key => $column) {
            if ($column->id() === 'action' or $column instanceof RestfulButton) {
                $columns->forget($key);

                continue;
            }

            $column->sortable(false)->searchable(false);
        }

        $table->source($this->overriddenSource ?? $this->resolve($source));

        return \PDF::loadView(
            'suitable::layouts.pdf',
            ['table' => $table->render('suitable::table')]
        )->stream($this->filename);
    }
}
