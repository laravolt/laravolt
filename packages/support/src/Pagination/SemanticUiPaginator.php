<?php namespace Laravolt\Support\Pagination;

use Illuminate\Pagination\LengthAwarePaginator;

class SemanticUiPaginator
{
    /**
     * @var LengthAwarePaginator
     */
    protected $paginator;

    /**
     * The default pagination view.
     *
     * @var string
     */
    public static $defaultView = 'support::pagination.semantic-ui';

    /**
     * SemanticUiPaginator constructor.
     * @param LengthAwarePaginator $paginator
     */
    public function __construct(LengthAwarePaginator $paginator)
    {

        $this->paginator = $paginator;
    }

    public function render($class = null)
    {
        return $this->paginator->render(static::$defaultView);
    }

    public function summary()
    {
        $from = (($this->paginator->currentPage() - 1) * $this->paginator->perPage()) + 1;
        $total = $this->paginator->total();

        if ($this->paginator->hasMorePages()) {
            $to = $from + $this->paginator->perPage() - 1;
        } else {
            $to = $total;
        }

        if ($total > 0) {
            return trans('support::pagination.summary', compact('from', 'to', 'total'));
        }

        return trans('support::pagination.empty');
    }

    public function pager()
    {
        $page = $this->paginator->currentPage();
        $total = max(1, ceil($this->paginator->total() / $this->paginator->perPage()));

        return trans('support::pagination.pager', compact('page', 'total'));
    }

    public function sequence($item)
    {
        $collections = collect($this->paginator->items());
        $index = $collections->search($item) + 1;
        $start = (request('page', 1) - 1) * $this->paginator->perPage();

        return $start + $index;
    }
}
