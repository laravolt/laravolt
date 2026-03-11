<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class SearchBox extends Component
{
    public string $id;
    public ?string $placeholder;
    public ?string $value;
    public ?string $action;
    public ?string $name;
    public ?string $size;
    public bool $autofocus;
    public ?string $shortcutKey;

    public function __construct(
        ?string $id = null,
        ?string $placeholder = null,
        ?string $value = null,
        ?string $action = null,
        ?string $name = null,
        ?string $size = null,
        ?bool $autofocus = null,
        ?string $shortcutKey = null
    ) {
        $this->id = $id ?? 'searchbox-' . uniqid();
        $this->placeholder = $placeholder ?? 'Search...';
        $this->value = $value;
        $this->action = $action;
        $this->name = $name ?? 'q';
        $this->size = $size ?? 'md';
        $this->autofocus = $autofocus ?? false;
        $this->shortcutKey = $shortcutKey;
    }

    public function render()
    {
        return view('laravolt::components.searchbox');
    }
}
