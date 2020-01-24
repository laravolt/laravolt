<?php

namespace Laravolt\Suitable\Plugins;

use Laravolt\Suitable\Builder;
use Laravolt\Suitable\Concerns\SourceOverridden;
use Laravolt\Suitable\Toolbars\Action;

class Pdf extends Plugin implements \Laravolt\Suitable\Contracts\Plugin
{
    use SourceOverridden;

    protected $shouldResponse = false;

    protected $filename = 'test.pdf';

    /**
     * Pdf constructor.
     *
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
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
        $table->source($this->overriddenSource ?? $this->resolve($source));

        return \niklasravnsborg\LaravelPdf\Facades\Pdf
            ::loadView('suitable::layouts.pdf', ['table' => $table->render('suitable::table')])
            ->stream($this->filename);
    }
}
