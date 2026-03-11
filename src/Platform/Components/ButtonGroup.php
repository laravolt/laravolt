<?php
declare(strict_types=1);
namespace Laravolt\Platform\Components;
use Illuminate\View\Component;

class ButtonGroup extends Component
{
    public ?string $size;
    public ?string $variant;

    public function __construct(?string $size = null, ?string $variant = null)
    {
        $this->size = $size ?? 'md';
        $this->variant = $variant ?? 'default';
    }

    public function render() { return view('laravolt::components.button-group'); }
}
