<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Item extends Component
{
    public function render()
    {
        return match (ComponentManager::$currentComponent) {
            Breadcrumb::class => $this->itemBreadcrumb(),
            default => $this->itemDefault()
        };
    }

    protected function itemDefault()
    {
        return <<<'blade'
        <div {{ $attributes->merge(['class' => 'item'])}} >
            {!! $slot !!}
        </div>
        blade;
    }

    protected function itemBreadcrumb()
    {
        return <<<'blade'
        <div {{ $attributes->merge(['class' => 'section'])}} >
            {!! $slot !!}
        </div>
        <i class="right angle icon divider"></i>
        blade;
    }
}
