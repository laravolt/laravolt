@php
    $separator = $attributes->get('separator', '/');
    $attributes = $attributes->except(['separator']);
@endphp

@section('breadcrumb')
    <nav {{ $attributes->merge(['class' => 'flex', 'aria-label' => 'Breadcrumb']) }}>
        <ol class="inline-flex items-center space-x-1 md:space-x-3 rtl:space-x-reverse">
            @if($slot)
                {!! $slot !!}
            @endif
        </ol>
    </nav>
@endsection

{{-- Blade component usage example --}}
@unless($slot)
    @php
        $items = $attributes->get('items', []);
        $attributes = $attributes->except(['items']);
    @endphp

    <nav {{ $attributes->merge(['class' => 'flex', 'aria-label' => 'Breadcrumb']) }}>
        <ol class="inline-flex items-center space-x-1 md:space-x-3 rtl:space-x-reverse">
            @foreach($items as $index => $item)
                <li class="inline-flex items-center">
                    @if($index > 0)
                        <svg class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400 dark:text-neutral-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                    @endif

                    @if(isset($item['url']) && $item['url'])
                        <a href="{{ $item['url'] }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-neutral-400 dark:hover:text-white">
                            @if(isset($item['icon']))
                                <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="{{ $item['icon'] }}"/>
                                </svg>
                            @endif
                            {{ $item['title'] }}
                        </a>
                    @else
                        <span class="inline-flex items-center text-sm font-medium text-gray-500 dark:text-neutral-500">
                            @if(isset($item['icon']))
                                <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="{{ $item['icon'] }}"/>
                                </svg>
                            @endif
                            {{ $item['title'] }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endunless
