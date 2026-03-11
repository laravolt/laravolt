<?php
declare(strict_types=1);
namespace Laravolt\Platform\Components;
use Illuminate\View\Component;

class ContextMenu extends Component
{
    public string $id;
    public array $items;

    public function __construct(?string $id = null, ?array $items = null)
    {
        $this->id = $id ?? 'context-menu-' . uniqid();
        $this->items = $items ?? [];
    }

    public function render() { return view('laravolt::components.context-menu'); }
}
