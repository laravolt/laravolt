<div class="menu attached right bottom" data-role="pagination">
    <!-- Previous Page Link -->
    @if ($paginator->onFirstPage())
        <button class="item disabled prev ui button icon">
            <i class="icon left chevron" aria-hidden="true"></i>
        </button>
    @else
        <div class="item prev ui button icon" wire:click.prevent="previousPage" rel="prev">
            <i class="icon left chevron" aria-hidden="true"></i>
        </div>
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
                    <div class="item active number">{{ $page }}</div>
                @else
                    <button class="item number" wire:click.prevent="gotoPage({{ $page }})">{{ $page }}</button>
                @endif
            @endforeach
        @endif
    @endforeach

<!-- Next Page Link -->
    @if ($paginator->hasMorePages())
        <button class="item next ui button icon" wire:click.prevent="nextPage" rel="next">
            <i class="icon right chevron" aria-hidden="true"></i>
        </button>
    @else
        <div class="item disabled next ui button icon">
            <i class="icon right chevron" aria-hidden="true"></i>
        </div>
    @endif
</div>
