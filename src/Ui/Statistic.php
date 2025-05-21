<?php

namespace Laravolt\Ui;

use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class Statistic extends Component
{
    public int|string $value = '';

    public string $title = '';

    public string $label = '';

    public ?string $icon = null;

    public ?string $color = null;

    /**
     * Cache duration in seconds.
     * Default: 1 hour
     */
    protected int $cacheDuration = 3600;

    /**
     * Custom cache key. If null, a key will be auto-generated.
     */
    protected ?string $cacheKey = null;

    public function value(): int|string
    {
        return $this->value;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function icon(): ?string
    {
        return $this->icon;
    }

    public function color(): ?string
    {
        return $this->color ?? config('laravolt.ui.color');
    }

    /**
     * Load data with caching applied.
     * This method should be overridden in child classes that need to fetch data.
     *
     * @param callable $callback The function that fetches data
     * @param string|null $customKey Optional custom cache key
     * @param int|null $duration Optional custom cache duration
     * @return mixed
     */
    protected function loadWithCache(callable $callback, ?string $customKey = null, ?int $duration = null): mixed
    {
        $key = $customKey ?? $this->getCacheKey();
        $cacheDuration = $duration ?? $this->cacheDuration;

        return Cache::remember(
            $key,
            $cacheDuration,
            $callback
        );
    }

    /**
     * Generate or get a cache key for this statistic
     */
    protected function getCacheKey(): string
    {
        if ($this->cacheKey) {
            return $this->cacheKey;
        }

        // Generate a cache key based on class name and any parameters
        return 'laravolt_statistic_' . class_basename($this) . '_' . md5(serialize($this));
    }

    public function render()
    {
        return view('laravolt::ui-component.statistic');
    }
}
