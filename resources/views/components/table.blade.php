@php
    $striped = $attributes->get('striped', false);
    $bordered = $attributes->get('bordered', true);
    $hover = $attributes->get('hover', true);
    $responsive = $attributes->get('responsive', true);
    $size = $attributes->get('size', 'md');
    $attributes = $attributes->except(['striped', 'bordered', 'hover', 'responsive', 'size']);

    // Build table classes
    $tableClasses = 'w-full divide-y divide-gray-200 dark:divide-neutral-700';

    if ($bordered) {
        $tableClasses .= ' border border-gray-200 dark:border-neutral-700';
    }

    // Row classes
    $rowClasses = 'divide-x divide-gray-200 dark:divide-neutral-700';

    if ($striped) {
        $rowClasses .= ' even:bg-gray-50 dark:even:bg-neutral-800';
    }

    if ($hover) {
        $rowClasses .= ' hover:bg-gray-50 dark:hover:bg-neutral-800';
    }

    // Cell classes
    $cellClasses = 'px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-neutral-200';

    // Header cell classes
    $headerClasses = 'px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:bg-neutral-800 dark:text-neutral-400';
@endphp

@if($responsive)
    <div class="overflow-x-auto">
@endif

<table {{ $attributes->merge(['class' => $tableClasses]) }}>
    @if(isset($headers) || $slot)
        <thead class="bg-gray-50 dark:bg-neutral-800">
            <tr>
                @if(isset($headers))
                    @foreach($headers as $header)
                        @php
                            $sortable = $header['sortable'] ?? false;
                            $sortDirection = $header['sort'] ?? null;
                            $sortKey = $header['key'] ?? null;
                        @endphp
                        <th scope="col" class="{{ $headerClasses }}">
                            @if($sortable)
                                <button
                                    type="button"
                                    class="group inline-flex items-center gap-x-1 hover:text-gray-700 dark:hover:text-neutral-300"
                                    @click="sortBy('{{ $sortKey }}')"
                                >
                                    {{ $header['label'] }}
                                    @if($sortDirection === 'asc')
                                        <svg class="w-4 h-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        </svg>
                                    @elseif($sortDirection === 'desc')
                                        <svg class="w-4 h-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 dark:text-neutral-500 dark:group-hover:text-neutral-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4 4 4m6 0v12m0 0 4-4m-4 4-4-4" />
                                        </svg>
                                    @endif
                                </button>
                            @else
                                {{ $header['label'] }}
                            @endif
                        </th>
                    @endforeach
                @else
                    {{ $slot }}
                @endif
            </tr>
        </thead>
    @endif

    @if(isset($rows) || $slot)
        <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-900 dark:divide-neutral-700">
            @if(isset($rows))
                @foreach($rows as $row)
                    <tr class="{{ $rowClasses }}">
                        @foreach($headers as $index => $header)
                            @php
                                $key = $header['key'] ?? $index;
                                $value = $row[$key] ?? '';
                                $actions = $header['actions'] ?? false;
                            @endphp

                            @if($actions && $index === count($headers) - 1)
                                <td class="{{ $cellClasses }}">
                                    <div class="flex items-center gap-x-2">
                                        @if(isset($row['actions']))
                                            @foreach($row['actions'] as $action)
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center gap-x-1.5 py-1.5 px-2 text-xs font-medium rounded-md {{ $action['variant'] === 'danger' ? 'text-red-700 bg-red-50 hover:bg-red-100 dark:text-red-400 dark:bg-red-900/20 dark:hover:bg-red-900/30' : 'text-gray-700 bg-gray-50 hover:bg-gray-100 dark:text-neutral-300 dark:bg-neutral-800 dark:hover:bg-neutral-700' }}"
                                                    @click="{{ $action['action'] ?? '' }}"
                                                >
                                                    @if(isset($action['icon']))
                                                        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"/>
                                                        </svg>
                                                    @endif
                                                    {{ $action['label'] ?? '' }}
                                                </button>
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                            @else
                                <td class="{{ $cellClasses }}">
                                    @if(is_array($value))
                                        {!! $value['html'] ?? $value['text'] ?? '' !!}
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            @else
                {{ $slot }}
            @endif
        </tbody>
    @endif
</table>

@if($responsive)
    </div>
@endif
