<?php
declare(strict_types=1);
namespace Laravolt\Platform\Components;
use Illuminate\View\Component;

class DragDrop extends Component
{
    public string $id;
    public string $group;
    public bool $sortable;
    public ?string $handle;

    public function __construct(?string $id = null, ?string $group = null, ?bool $sortable = null, ?string $handle = null)
    {
        $this->id = $id ?? 'drag-drop-' . uniqid();
        $this->group = $group ?? 'default';
        $this->sortable = $sortable ?? true;
        $this->handle = $handle;
    }

    public function render() { return view('laravolt::components.drag-drop'); }
}
