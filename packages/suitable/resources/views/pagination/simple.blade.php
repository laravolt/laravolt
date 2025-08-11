<nav class="inline-flex items-center gap-1" data-role="pagination" aria-label="Pagination">
  @if ($paginator->onFirstPage())
    <span class="px-2 py-1.5 text-xs rounded-lg text-gray-400 border border-gray-200 bg-white dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-500 cursor-not-allowed">Prev</span>
  @else
    <a class="px-2 py-1.5 text-xs rounded-lg text-gray-700 border border-gray-200 bg-white hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300" href="{{ $paginator->previousPageUrl() }}" rel="prev">Prev</a>
  @endif

  @if ($paginator->hasMorePages())
    <a class="px-2 py-1.5 text-xs rounded-lg text-gray-700 border border-gray-200 bg-white hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>
  @else
    <span class="px-2 py-1.5 text-xs rounded-lg text-gray-400 border border-gray-200 bg-white dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-500 cursor-not-allowed">Next</span>
  @endif
</nav>
