<div class="menu attached right bottom" data-role="pagination">
    <!-- Previous Page Link -->
    @if ($paginator->onFirstPage())
        <div class="item disabled prev"><i class="icon left chevron"></i></div>
    @else
        <a class="item prev" href="{{ $paginator->previousPageUrl() }}" rel="prev"><i class="icon left chevron"></i></a>
    @endif

    <!-- Pagination Elements -->
    @foreach ($elements as $element)
    <!-- "Three Dots" Separator -->
        @if (is_string($element))
            <div class="item disabled dots"><span>{{ $element }}</span></div>
        @endif

    <!-- Array Of Links -->
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <a class="item active number">{{ $page }}</a>
                @else
                    <a class="item number" href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    <!-- Next Page Link -->
    @if ($paginator->hasMorePages())
        <a class="item next" href="{{ $paginator->nextPageUrl() }}" rel="next"><i class="icon right chevron"></i></a>
    @else
        <div class="item disabled next"><i class="icon right chevron"></i></div>
    @endif
</div>
