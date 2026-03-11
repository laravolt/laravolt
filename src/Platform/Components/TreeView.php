<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class TreeView extends Component
{
    public string $id;
    public array $items;
    public bool $selectable;
    public bool $collapsible;
    public ?string $selectedValue;
    public bool $checkbox;

    public function __construct(
        ?string $id = null,
        ?array $items = null,
        ?bool $selectable = null,
        ?bool $collapsible = null,
        ?string $selectedValue = null,
        ?bool $checkbox = null
    ) {
        $this->id = $id ?? 'tree-view-' . uniqid();
        $this->items = $items ?? [];
        $this->selectable = $selectable ?? false;
        $this->collapsible = $collapsible ?? true;
        $this->selectedValue = $selectedValue;
        $this->checkbox = $checkbox ?? false;
    }

    public function render()
    {
        return view('laravolt::components.tree-view');
    }
}
