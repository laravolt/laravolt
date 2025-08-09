<div class="flex items-center gap-x-1" data-role="pagination">
    @if ($paginator->onFirstPage())
        <button class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white p-2 text-gray-400" disabled>
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
    @else
        <button class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white p-2 text-gray-700 hover:bg-gray-50" wire:click.prevent="previousPage" rel="prev">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
    @endif

    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="px-2 text-sm text-gray-500">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="inline-flex items-center rounded-md bg-teal-600 px-2.5 py-1.5 text-sm font-medium text-white">{{ $page }}</span>
                @else
                    <button class="inline-flex items-center rounded-md border border-gray-300 bg-white px-2.5 py-1.5 text-sm text-gray-700 hover:bg-gray-50" wire:click.prevent="gotoPage({{ $page }})">{{ $page }}</button>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <button class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white p-2 text-gray-700 hover:bg-gray-50" wire:click.prevent="nextPage" rel="next">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    @else
        <button class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white p-2 text-gray-400" disabled>
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    @endif
</div>
