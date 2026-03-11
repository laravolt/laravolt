<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class FileUpload extends Component
{
    public string $id;
    public string $name;
    public bool $multiple;
    public ?string $accept;
    public ?int $maxSize;
    public ?int $maxFiles;
    public bool $preview;
    public bool $dragDrop;
    public bool $disabled;

    public function __construct(
        ?string $id = null,
        ?string $name = null,
        ?bool $multiple = null,
        ?string $accept = null,
        ?int $maxSize = null,
        ?int $maxFiles = null,
        ?bool $preview = null,
        ?bool $dragDrop = null,
        ?bool $disabled = null
    ) {
        $this->id = $id ?? 'file-upload-' . uniqid();
        $this->name = $name ?? 'files';
        $this->multiple = $multiple ?? false;
        $this->accept = $accept;
        $this->maxSize = $maxSize;
        $this->maxFiles = $maxFiles;
        $this->preview = $preview ?? true;
        $this->dragDrop = $dragDrop ?? true;
        $this->disabled = $disabled ?? false;
    }

    public function render()
    {
        return view('laravolt::components.file-upload');
    }
}
