<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Editor extends Component
{
    public string $id;
    public string $name;
    public ?string $value;
    public ?string $placeholder;
    public ?int $minHeight;
    public array $toolbar;
    public bool $disabled;

    public function __construct(
        ?string $id = null,
        ?string $name = null,
        ?string $value = null,
        ?string $placeholder = null,
        ?int $minHeight = null,
        ?array $toolbar = null,
        ?bool $disabled = null
    ) {
        $this->id = $id ?? 'editor-' . uniqid();
        $this->name = $name ?? $this->id;
        $this->value = $value;
        $this->placeholder = $placeholder ?? 'Write something...';
        $this->minHeight = $minHeight ?? 200;
        $this->toolbar = $toolbar ?? ['bold', 'italic', 'underline', 'strike', 'link', 'ol', 'ul', 'blockquote', 'code'];
        $this->disabled = $disabled ?? false;
    }

    public function render()
    {
        return view('laravolt::components.editor');
    }
}
