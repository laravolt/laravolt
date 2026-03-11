<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\View\Component;

class Datatable extends Component
{
    public string $id;
    public array $columns;
    public array $data;
    public bool $search;
    public bool $sorting;
    public int $pageLength;
    public array $pageLengths;
    public ?string $ajaxUrl;

    public function __construct(
        ?string $id = null,
        ?array $columns = null,
        ?array $data = null,
        ?bool $search = null,
        ?bool $sorting = null,
        ?int $pageLength = null,
        ?array $pageLengths = null,
        ?string $ajaxUrl = null
    ) {
        $this->id = $id ?? 'datatable-' . uniqid();
        $this->columns = $columns ?? [];
        $this->data = $data ?? [];
        $this->search = $search ?? true;
        $this->sorting = $sorting ?? true;
        $this->pageLength = $pageLength ?? 10;
        $this->pageLengths = $pageLengths ?? [5, 10, 25, 50];
        $this->ajaxUrl = $ajaxUrl;
    }

    public function render()
    {
        return view('laravolt::components.datatable');
    }
}
