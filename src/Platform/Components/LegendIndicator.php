<?php
declare(strict_types=1);
namespace Laravolt\Platform\Components;
use Illuminate\View\Component;

class LegendIndicator extends Component
{
    public array $items;
    public ?string $layout;

    public function __construct(?array $items = null, ?string $layout = null)
    {
        $this->items = $items ?? [];
        $this->layout = $layout ?? 'horizontal';
    }

    public function render() { return view('laravolt::components.legend-indicator'); }
}
