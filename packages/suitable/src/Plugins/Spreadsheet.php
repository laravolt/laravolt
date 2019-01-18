<?php

namespace Laravolt\Suitable\Plugins;

use Laravolt\Suitable\Builder;
use Laravolt\Suitable\Toolbars\Action;

class Spreadsheet extends Plugin implements \Laravolt\Suitable\Contracts\Plugin
{
    protected $shouldResponse = false;

    protected $filename = 'spreadsheet.csv';

    protected $format = null;

    /**
     * Spreadsheet constructor.
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
        $this->format = array_get(pathinfo($this->filename), 'extension', 'csv');
    }

    public function init()
    {
        $this->shouldResponse = request('format') === $this->format;
    }

    public function shouldResponse()
    : bool
    {
        return $this->shouldResponse;
    }

    public function decorate(Builder $table)
    : Builder {
        $url = request()->url().'?'.http_build_query(array_merge(request()->input(), ['format' => $this->format]));

        $segment = $table->getDefaultSegment();
        $segment->addLeft(Action::make('Export To '.title_case($this->format), $url));

        return $table;
    }

    public function resolve($source)
    {
        if ($source instanceof \Illuminate\Database\Eloquent\Builder) {
            return $source->get();
        }

        return $source;
    }

    public function response($source, Builder $table)
    {
        $source = $this->resolve($source)->map->only($this->only);

        switch ($this->format) {
            case 'xls':
            case 'xlsx':

                return fastexcel($source)->download($this->filename);
                break;

            case 'csv':
            default:

                return fastexcel($source)->configureCsv(';', '#', '\n', 'gbk')->download($this->filename);
        }
    }
}
