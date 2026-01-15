<?php

declare(strict_types=1);

namespace Laravolt\Platform\Components;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\View\Component;

class Pagination extends Component
{
    public $paginator = null;

    public $links = null;

    public $currentPage = 1;

    public $totalPages = 10;

    public function __construct(
        ?LengthAwarePaginator $paginator = null,
        ?array $links = null,
        ?int $currentPage = null,
        ?int $totalPages = null
    ) {
        $this->paginator = $paginator;
        $this->links = $links;
        $this->currentPage = $currentPage ?? $this->currentPage;
        $this->totalPages = $totalPages ?? $this->totalPages;
    }

    public function render()
    {
        return view('laravolt::components.pagination');
    }
}
