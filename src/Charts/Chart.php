<?php

namespace Laravolt\Charts;

use Illuminate\Support\Str;
use Livewire\Component;

abstract class Chart extends Component
{
    public const AREA = 'area';
    public const BAR = 'bar';
    public const DONUT = 'donut';
    public const LINE = 'line';
    public const PIE = 'pie';

    public string $key;

    protected string $title = '';

    protected string $type = self::LINE;

    protected int $height = 350;

    /**
     * Sembunyikan semua element pendukung chart seperti sumbu x-y dan label.
     * Cocok untuk menampilkan chart dalam area yang sempit.
     * @link https://apexcharts.com/docs/options/chart/sparkline/
     */
    protected bool $sparkline = false;

    protected array $series = [];

    abstract protected function series(): array;

    public function labels(): array
    {
        return array_keys(collect($this->series)->last());
    }

    public function title(): string
    {
        return $this->title;
    }

    public function height(): int
    {
        return $this->height;
    }

    public function mount(): void
    {
        $this->key = 'chart-'.Str::uuid();
        $this->series = $this->series();
    }

    public function options(): array
    {
        return [
            'series' => $this->formatSeries(),
            'labels' => $this->labels(),
            'chart' => [
                'height' => $this->height(),
                'type' => $this->type,
                'zoom' => [
                    'enabled' => false,
                ],
                'toolbar' => ['show' => false],
                'sparkline' => ['enabled' => $this->sparkline],
            ],
            // 'xaxis' => [
            //     'categories' => $this->labels(),
            // ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 2,
                'lineCap' => 'round',
            ],
        ];
    }

    protected function formatSeries(): array
    {
        return collect($this->series)
            ->transform(fn ($data, $name) => ['name' => $name, 'data' => array_values($data)])
            ->values()
            ->toArray();
    }

    public function render()
    {
        if ($this->sparkline) {
            return view('laravolt::ui-component.charts.sparkline');
        }

        return view('laravolt::ui-component.charts.chart');
    }
}
