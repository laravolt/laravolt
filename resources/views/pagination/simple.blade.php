<div class="ui buttons basic icon mini" themed>
    <!-- Previous Page Link -->
    @if ($paginator->onFirstPage())
        <div class="ui button disabled "><i class="icon left chevron"></i></div>
    @else
        <a class="ui button " href="{{ $paginator->previousPageUrl() }}" rel="prev"><i
                    class="icon left chevron"></i></a>
    @endif
    <div class="ui button ">{{ $paginator->currentPage() }}/{{ $paginator->lastPage() }}</div>
    <!-- Next Page Link -->
    @if ($paginator->hasMorePages())
        <a class="ui button " href="{{ $paginator->nextPageUrl() }}" rel="next"><i class="icon right chevron"></i></a>
    @else
        <div class="ui button disabled "><i class="icon right chevron"></i></div>
    @endif
</div>
