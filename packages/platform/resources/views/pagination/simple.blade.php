<div class="ui pagination menu">
    <!-- Previous Page Link -->
    @if ($paginator->onFirstPage())
        <div class="item disabled">@lang('suitable::pagination.previous')</div>
    @else
        <a class="item" href="{{ $paginator->previousPageUrl() }}" rel="prev">@lang('suitable::pagination.previous')</a>
    @endif

<!-- Next Page Link -->
    @if ($paginator->hasMorePages())
        <a class="item" href="{{ $paginator->nextPageUrl() }}" rel="next">@lang('suitable::pagination.next')</a>
    @else
        <div class="item disabled">@lang('suitable::pagination.next')</div>
    @endif
</div>
