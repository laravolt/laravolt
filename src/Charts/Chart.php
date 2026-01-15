<?php

declare(strict_types=1);

namespace Laravolt\Charts;

use Illuminate\Support\Facades\Cache;
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
     *
     * @link https://apexcharts.com/docs/options/chart/sparkline/
     */
    protected bool $sparkline = false;

    protected array $series = [];

    /**
     * Cache duration in seconds.
     * Default: 1 hour
     */
    protected int $cacheDuration = 3600;

    /**
     * Custom cache key. If null, a key will be auto-generated.
     */
    protected ?string $cacheKey = null;

    /**
     * Get chart series data. This is the method that should be implemented by child classes
     * to provide the chart data.
     */
    abstract public function series(): array;

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
        $this->series = $this->loadSeriesData();
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

    public function render()
    {
        if ($this->sparkline) {
            return view('laravolt::ui-component.charts.sparkline');
        }

        return view('laravolt::ui-component.charts.chart');
    }

    /**
     * Load series data with caching applied.
     * All select queries are cached by default.
     */
    protected function loadSeriesData(): array
    {
        $key = $this->getCacheKey();

        return Cache::remember(
            $key,
            $this->cacheDuration,
            fn () => $this->series()
        );
    }

    /**
     * Generate or get a cache key for this chart
     */
    protected function getCacheKey(): string
    {
        if ($this->cacheKey) {
            return $this->cacheKey;
        }

        // Generate a cache key based on class name and relevant properties
        return 'laravolt_chart_'.class_basename($this).'_'.md5(json_encode([
            'title' => $this->title,
            'type' => $this->type,
            'height' => $this->height,
            'sparkline' => $this->sparkline,
            'series' => $this->series,
        ]));
    }

    protected function formatSeries(): array
    {
        return collect($this->series)
            ->transform(fn ($data, $name) => ['name' => $name, 'data' => array_values($data)])
            ->values()
            ->toArray();
    }
}
