<?php
declare(strict_types=1);
namespace Laravolt\Platform\Components;
use Illuminate\View\Component;

class InputGroup extends Component
{
    public ?string $prefix;
    public ?string $suffix;
    public ?string $prefixIcon;
    public ?string $suffixIcon;
    public ?string $size;

    public function __construct(?string $prefix = null, ?string $suffix = null, ?string $prefixIcon = null, ?string $suffixIcon = null, ?string $size = null)
    {
        $this->prefix = $prefix;
        $this->suffix = $suffix;
        $this->prefixIcon = $prefixIcon;
        $this->suffixIcon = $suffixIcon;
        $this->size = $size ?? 'md';
    }

    public function render() { return view('laravolt::components.input-group'); }
}
