<div class="ui buttons basic icon mini" themed>

    @if ($paginator->onFirstPage())
        <div class="ui button disabled "><i class="icon left chevron" aria-hidden="true"></i></div>
    @else
        <button class="ui button" wire:click.prevent="previousPage">
            <i class="icon left chevron" aria-hidden="true"></i>
        </button>
    @endif

    <div class="ui button ">{{ $paginator->currentPage() }}/{{ $paginator->lastPage() }}</div>

    @if ($paginator->hasMorePages())
        <button class="ui button" wire:click.prevent="nextPage">
            <i class="icon right chevron" aria-hidden="true"></i>
        </button>
    @else
        <div class="ui button disabled">
            <i class="icon right chevron" aria-hidden="true"></i>
        </div>
    @endif
</div>
