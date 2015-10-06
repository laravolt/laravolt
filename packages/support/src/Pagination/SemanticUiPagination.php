<?php namespace Laravolt\Support\Pagination;

use Illuminate\Contracts\Pagination\Presenter;
use Illuminate\Pagination\BootstrapThreePresenter;

class SemanticUiPagination extends BootstrapThreePresenter implements Presenter
{

    protected function getActivePageWrapper($text)
    {
        return '<div class="active item">' . $text . '</div>';
    }

    protected function getDisabledTextWrapper($text)
    {
        return '<div class="disabled item">' . $text . '</div>';
    }

    protected function getAvailablePageWrapper($url, $page, $rel = null)
    {

        return '<a class="item" href="' . htmlentities($url) . '">' . $page . '</a>';

        return '<li><a href="' . htmlentities($url) . '"' . $rel . '>' . $page . '</a></li>';
    }

    public function render($class = null)
    {
        if ($this->hasPages()) {
            return sprintf(
                '<div class="menu %s">%s</div>',
                $class,
                $this->getLinks()
            );
        }

        return '';
    }

    public function summary()
    {
        $from = (($this->paginator->currentPage() - 1) * $this->paginator->perPage()) + 1;

        if ($this->paginator->hasMorePages()) {
            $to = $from + $this->paginator->perPage() - 1;
        } else {
            $to = $this->paginator->total();
        }

        return trans('support::pagination.summary', ['from' => $from, 'to' => $to, "total" => $this->paginator->total()]);
    }

}
