@php
    $hasCover = $attributes['cover'] ?? false;
    $hasHeader = $title || $content || ($attributes['meta.before'] ?? false) || ($attributes['meta.after'] ?? false);
    $attributes = $attributes->except(['cover', 'meta.before', 'meta.after']);

    $cardClasses = 'bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 dark:bg-neutral-800 dark:border-neutral-700';
@endphp

@isset($url)
    <a href="{{ $url }}" {{ $attributes->merge(['class' => $cardClasses]) }}>
@else
    <div {{ $attributes->merge(['class' => $cardClasses]) }}>
@endisset

    @if($hasCover)
        <div class="relative">
            <img src="{{ $attributes['cover'] }}" alt="" class="w-full h-48 object-cover rounded-t-xl">
        </div>
    @endif

    @if($hasHeader)
        <div class="p-6 {{ $hasCover ? '' : 'pt-6' }}">
            @if($attributes['meta.before'] ?? false)
                <div class="text-sm text-gray-500 mb-2 dark:text-neutral-400">{!! $attributes['meta.before'] !!}</div>
            @endif

            @if($title)
                <h3 class="text-lg font-semibold text-gray-900 mb-2 dark:text-white">{{ $title }}</h3>
            @endif

            @if($attributes['meta.after'] ?? false)
                <div class="text-sm text-gray-500 mb-3 dark:text-neutral-400">{!! $attributes['meta.after'] !!}</div>
            @endif

            @if($content)
                <p class="text-gray-700 dark:text-neutral-300">{{ $content }}</p>
            @endif
        </div>
    @endif

    @if($body ?? null)
        <div class="px-6 pb-6">
            {{ $body }}
        </div>
    @endif

    @if($slot)
        <div class="px-6 pb-6">
            {{ $slot }}
        </div>
    @endif

@isset($url)
    </a>
@else
    </div>
@endisset
