<?php

namespace Laravolt\Suitable\Plugins;

use Laravolt\Suitable\Builder;
use Laravolt\Suitable\Toolbars\Action;

class Pdf implements \Laravolt\Suitable\Contracts\Plugin
{
    protected $wantsPdf = false;

    public function init()
    {
        $this->wantsPdf = request('format') === 'pdf';
    }

    public function resolve($source)
    {
        if (!$this->wantsPdf) {
            return $source;
        }

        if ($source instanceof \Illuminate\Database\Eloquent\Builder) {
            return $source->get();
        }
    }

    public function decorate(Builder $table): Builder
    {
        $url = request()->url().'?'.http_build_query(array_merge(request()->input(), ['format' => 'pdf']));

        $segment = $table->getDefaultSegment();
        $segment->addLeft(Action::make('Export To Pdf', $url));

        return $table;
    }

    public function response()
    {
        return \niklasravnsborg\LaravelPdf\Facades\Pdf
            ::loadView('suitable::layouts.pdf', [$this->alias => $this->table('pdf')])
            ->stream('test.pdf');
    }
}
