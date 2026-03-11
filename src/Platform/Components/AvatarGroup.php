<?php
declare(strict_types=1);
namespace Laravolt\Platform\Components;
use Illuminate\View\Component;

class AvatarGroup extends Component
{
    public array $avatars;
    public ?int $max;
    public ?string $size;

    public function __construct(?array $avatars = null, ?int $max = null, ?string $size = null)
    {
        $this->avatars = $avatars ?? [];
        $this->max = $max;
        $this->size = $size ?? 'md';
    }

    public function render() { return view('laravolt::components.avatar-group'); }
}
