<div class="menu attached right bottom" data-role="pagination">
    <!-- Previous Page Link -->
    @if ($paginator->onFirstPage())
        <div class="item disabled"><i class="icon left chevron"></i></div>
    @else
        <a class="item" href="{{ $paginator->previousPageUrl() }}" rel="prev"><i class="icon left chevron"></i></a>
    @endif

    <!-- Next Page Link -->
    @if ($paginator->hasMorePages())
        <a class="item" href="{{ $paginator->nextPageUrl() }}" rel="next"><i class="icon right chevron"></i></a>
    @else
        <div class="item disabled"><i class="icon right chevron"></i></div>
    @endif
</div>
