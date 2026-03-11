<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class ComboBox extends Component
{
    public string $id;
    public string $name;
    public ?string $placeholder;
    public ?string $value;
    public array $options;
    public ?string $apiUrl;
    public int $minChars;
    public bool $disabled;
    public ?string $size;
    public ?string $groupField;

    public function __construct(
        ?string $id = null,
        ?string $name = null,
        ?string $placeholder = null,
        ?string $value = null,
        ?array $options = null,
        ?string $apiUrl = null,
        ?int $minChars = null,
        ?bool $disabled = null,
        ?string $size = null,
        ?string $groupField = null
    ) {
        $this->id = $id ?? 'combobox-' . uniqid();
        $this->name = $name ?? $this->id;
        $this->placeholder = $placeholder ?? 'Type to search...';
        $this->value = $value;
        $this->options = $options ?? [];
        $this->apiUrl = $apiUrl;
        $this->minChars = $minChars ?? 1;
        $this->disabled = $disabled ?? false;
        $this->size = $size ?? 'md';
        $this->groupField = $groupField;
    }

    public function render()
    {
        return view('laravolt::components.combobox');
    }
}
