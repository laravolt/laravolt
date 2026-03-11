<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class AdvancedSelect extends Component
{
    public string $id;
    public string $name;
    public ?string $placeholder;
    public ?string $value;
    public array $options;
    public bool $multiple;
    public bool $searchable;
    public bool $taggable;
    public bool $clearable;
    public ?string $size;
    public bool $disabled;

    public function __construct(
        ?string $id = null,
        ?string $name = null,
        ?string $placeholder = null,
        ?string $value = null,
        ?array $options = null,
        ?bool $multiple = null,
        ?bool $searchable = null,
        ?bool $taggable = null,
        ?bool $clearable = null,
        ?string $size = null,
        ?bool $disabled = null
    ) {
        $this->id = $id ?? 'advanced-select-' . uniqid();
        $this->name = $name ?? $this->id;
        $this->placeholder = $placeholder ?? 'Select option...';
        $this->value = $value;
        $this->options = $options ?? [];
        $this->multiple = $multiple ?? false;
        $this->searchable = $searchable ?? true;
        $this->taggable = $taggable ?? false;
        $this->clearable = $clearable ?? true;
        $this->size = $size ?? 'md';
        $this->disabled = $disabled ?? false;
    }

    public function render()
    {
        return view('laravolt::components.advanced-select');
    }
}
