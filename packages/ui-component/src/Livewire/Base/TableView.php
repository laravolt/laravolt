<?php

namespace Laravolt\UiComponent\Livewire\Base;

use Laravolt\Suitable\Concerns\SourceResolver;
use Laravolt\Suitable\Toolbars\Search;
use Livewire\Component;
use Livewire\WithPagination;

abstract class TableView extends Component
{
    use SourceResolver;
    use WithPagination;

    private const DEFAULT_PER_PAGE = 15;
    protected bool $showSearchbox = true;
    protected bool $showPerPage = true;
    protected string $searchName = 'search';
    protected string $title = '';
    protected $queryString = [
        'page' => ['except' => 1],
        'search' => ['except' => ''],
        'sort' => ['except' => null],
        'direction',
        'perPage' => ['except' => self::DEFAULT_PER_PAGE],
    ];
    public string $search = '';
    public int $perPage = self::DEFAULT_PER_PAGE;
    public ?string $sort = null;
    public ?string $direction = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function changePerPage($perPage)
    {
        $this->perPage = $perPage;
    }

    public function sortBy($column)
    {
        if ($this->sort !== $column) {
            $this->direction = null;
        }

        $this->sort = $column;
        $this->direction = match ($this->direction) {
            null, 'desc' => 'asc',
            'asc' => 'desc',
        };
    }

    public function render()
    {
        /** @var \Laravolt\UiComponent\TableBuilder $table */
        $table = app('laravolt.table.builder')->source($this->resolve($this->data()));

        if (is_string($this->title) && $this->title !== '') {
            $table->title($this->title);
        }

        $table->showPerPage($this->showPerPage);

        if ($this->showSearchbox) {
            $table->getDefaultSegment()->appendLeft(Search::make($this->searchName));
        }

        $table->columns($this->columns());

        return $table->render(
            [
                'sort' => $this->sort,
                'direction' => $this->direction,
            ]
        );
    }

    abstract protected function data();

    abstract protected function columns();
}
