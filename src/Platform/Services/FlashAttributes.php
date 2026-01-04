<?php

declare(strict_types=1);

namespace Laravolt\Platform\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FlashAttributes extends Collection
{
    protected array $types = [
        'info' => [
            'showIcon' => 'blue info',
            'classProgress' => 'blue',
        ],
        'success' => [
            'showIcon' => 'green checkmark',
            'classProgress' => 'green',
        ],
        'warning' => [
            'showIcon' => 'orange warning',
            'classProgress' => 'orange',
        ],
        'error' => [
            'showIcon' => 'red times',
            'classProgress' => 'red',
            'transition' => ['showMethod' => 'tada', 'showDuration' => 1000],
        ],
    ];

    public function __construct($items = [])
    {
        parent::__construct($items + $this->configAttributes() + $this->defaultAttributes());
    }

    public function setMessage(string $message, string $type)
    {
        $this->items['message'] = $message;
        $this->items['class'] = $this->types[$type]['class'] ?? $this->items['class'];
        $this->items['showIcon'] = $this->types[$type]['showIcon'] ?? null;
        $this->items['classProgress'] = $this->types[$type]['classProgress'] ?? null;
        if (isset($this->types[$type]['transition'])) {
            $this->items['transition'] = $this->types[$type]['transition'] + $this->items['transition'];
        }

        return $this;
    }

    private function defaultAttributes(): array
    {
        return [
            'message' => null,
            'class' => 'basic',
            'closeIcon' => false,
            'displayTime' => 'auto',
            'minDisplayTime' => 3000,
            'opacity' => 1,
            'position' => 'top center',
            'compact' => false,
            'showIcon' => false,
            'showProgress' => 'bottom',
            'progressUp' => false,
            'pauseOnHover' => true,
            'newestOnTop' => true,
            'transition' => [
                'showMethod' => 'fade',
                'showDuration' => 2000,
                'hideMethod' => 'fade',
                'hideDuration' => 1000,
            ],
        ];
    }

    private function configAttributes(): array
    {
        return collect(config('laravolt.ui.flash.attributes'))->mapWithKeys(
            function ($item, $key) {
                return [Str::camel($key) => $item];
            }
        )->toArray();
    }
}
