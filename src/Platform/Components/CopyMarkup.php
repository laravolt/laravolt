<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class CopyMarkup extends Component
{
    public $content = '';

    public $language = 'html';

    public $showCopyButton = true;

    public $showLineNumbers = false;

    public $theme = 'light';

    public $size = 'md';

    public function __construct(
        ?string $content = null,
        ?string $language = null,
        ?bool $showCopyButton = null,
        ?bool $showLineNumbers = null,
        ?string $theme = null,
        ?string $size = null
    ) {
        $this->content = $content ?? $this->content;
        $this->language = $language ?? $this->language;
        $this->showCopyButton = $showCopyButton ?? $this->showCopyButton;
        $this->showLineNumbers = $showLineNumbers ?? $this->showLineNumbers;
        $this->theme = $theme ?? $this->theme;
        $this->size = $size ?? $this->size;
    }

    public function render()
    {
        return view('laravolt::components.copy-markup');
    }
}
