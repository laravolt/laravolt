<div class="flex items-center gap-x-1" themed>

    @if ($paginator->onFirstPage())
        <button class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white p-2 text-gray-400" disabled>
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
    @else
        <button class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white p-2 text-gray-700 hover:bg-gray-50" wire:click.prevent="previousPage">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
    @endif

    <div class="inline-flex items-center rounded-md bg-gray-100 px-2.5 py-1.5 text-sm font-medium text-gray-700">{{ $paginator->currentPage() }}/{{ $paginator->lastPage() }}</div>

    @if ($paginator->hasMorePages())
        <button class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white p-2 text-gray-700 hover:bg-gray-50" wire:click.prevent="nextPage">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    @else
        <button class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white p-2 text-gray-400" disabled>
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    @endif
</div>
