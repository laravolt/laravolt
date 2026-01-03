<!-- Table -->
<table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700 basictable" aria-label="table">
    <thead class="bg-gray-50 dark:bg-neutral-700">
        <tr class="divide-x divide-gray-200 dark:border-neutral-700 dark:divide-neutral-700">
            @foreach ($columns as $column)
                @if ($column->getSortableColumn())
                    <th scope="col" class="min-w-40">
                        <!-- Sort Dropdown -->
                        <div class="hs-dropdown relative inline-flex w-full cursor-pointer basictable">
                            <button type="button"
                                class="px-4 py-2.5 text-start w-full flex items-center gap-x-1 text-sm text-nowrap font-normal text-gray-500 focus:outline-hidden focus:bg-gray-100 dark:text-neutral-500 dark:focus:bg-neutral-700"
                                wire:click.prevent="sortBy('{{ $column->getSortableColumn() }}')"
                                {!! $column->headerAttributes(asHtml: true) !!}>
                                {!! $column->getHeader() !!}
                                @if ($column->getSortableColumn() === $sort)
                                    <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        @if ($direction === 'asc')
                                            <path d="m5 12 7-7 7 7" />
                                            <path d="M12 19V5" />
                                        @else
                                            <path d="M12 5v14" />
                                            <path d="m19 12-7 7-7-7" />
                                        @endif
                                    </svg>
                                @else
                                    <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m7 15 5 5 5-5" />
                                        <path d="m7 9 5-5 5 5" />
                                    </svg>
                                @endif
                            </button>
                        </div>
                        <!-- End Sort Dropdown -->
                    </th>
                @else
                    <th scope="col"
                        class="px-4 py-2.5 text-start text-sm text-nowrap font-normal text-gray-500 dark:text-neutral-500"
                        {!! $column->headerAttributes(asHtml: true) !!}>
                        {!! $column->getHeader() !!}
                    </th>
                @endif
            @endforeach
        </tr>
    </thead>

    <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
        @forelse($data as $item)
            @php($outerLoop = $loop)
            <tr class="hover:bg-gray-100 dark:hover:bg-neutral-700">
                @foreach ($columns as $column)
                    <td class="whitespace-nowrap px-4 py-1" {!! $column->cellAttributes($item) !!}>
                        <span class="text-sm text-gray-600 dark:text-neutral-400">
                            {!! $column->cell($item, $data, $outerLoop) !!}
                        </span>
                    </td>
                @endforeach
            </tr>
        @empty
            @include('laravolt::ui-component.table-view.empty')
        @endforelse
    </tbody>
</table>
<!-- End Table -->
