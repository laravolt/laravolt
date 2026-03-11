@php
    $id = $id ?? 'datatable-' . uniqid();
    $columns = $columns ?? [];
    $data = $data ?? [];
    $search = $search ?? true;
    $sorting = $sorting ?? true;
    $pageLength = $pageLength ?? 10;
    $pageLengths = $pageLengths ?? [5, 10, 25, 50];
    $ajaxUrl = $ajaxUrl ?? null;
    $tableId = $id;
@endphp

<div class="flex flex-col" {{ $attributes->except(['columns', 'data', 'search', 'sorting', 'page-length', 'page-lengths', 'ajax-url']) }}>
    @if($search)
    <div class="py-3 flex justify-between items-center">
        <div class="relative max-w-xs">
            <label for="{{ $tableId }}-search" class="sr-only">Search</label>
            <input type="text" id="{{ $tableId }}-search"
                   class="py-2 ps-10 pe-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                   placeholder="Search...">
            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                <svg class="size-4 text-gray-400 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            </div>
        </div>
        <div class="flex items-center gap-x-2">
            <span class="text-sm text-gray-500 dark:text-neutral-400">Show</span>
            <select id="{{ $tableId }}-per-page" class="py-1.5 px-2.5 pe-9 block border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                @foreach($pageLengths as $len)
                    <option value="{{ $len }}" {{ $len === $pageLength ? 'selected' : '' }}>{{ $len }}</option>
                @endforeach
            </select>
            <span class="text-sm text-gray-500 dark:text-neutral-400">entries</span>
        </div>
    </div>
    @endif

    <div class="-m-1.5 overflow-x-auto">
        <div class="p-1.5 min-w-full inline-block align-middle">
            <div class="border rounded-lg overflow-hidden dark:border-neutral-700">
                <table id="{{ $tableId }}" class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                    <thead class="bg-gray-50 dark:bg-neutral-700">
                        <tr>
                            @foreach($columns as $col)
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400 {{ $sorting ? 'cursor-pointer select-none' : '' }}">
                                    <div class="flex items-center gap-x-2">
                                        {{ is_array($col) ? ($col['label'] ?? $col['key'] ?? '') : $col }}
                                        @if($sorting)
                                            <svg class="shrink-0 size-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/></svg>
                                        @endif
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                        @forelse($data as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700/50">
                                @foreach($columns as $col)
                                    @php $key = is_array($col) ? ($col['key'] ?? '') : $col; @endphp
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                        {{ data_get($row, $key, '') }}
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($columns) }}" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-neutral-400">
                                    No data available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination info --}}
    <div class="py-3 flex justify-between items-center">
        <p class="text-sm text-gray-500 dark:text-neutral-400">
            Showing <span class="font-medium text-gray-800 dark:text-neutral-200">{{ count($data) }}</span> entries
        </p>
    </div>
</div>

@if($search || $sorting)
@pushOnce('datatable-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[id^="datatable-"][id$="-search"]').forEach(function(searchInput) {
        var tableId = searchInput.id.replace('-search', '');
        var table = document.getElementById(tableId);
        if (!table) return;
        searchInput.addEventListener('input', function() {
            var filter = this.value.toLowerCase();
            var rows = table.querySelectorAll('tbody tr');
            rows.forEach(function(row) {
                var text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    });
});
</script>
@endPushOnce
@endif
