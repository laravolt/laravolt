@php
    $isActive = $activeClass ?? '';
    $tabClasses = $isActive
        ? 'border-b-2 border-blue-500 text-blue-600 dark:border-blue-400 dark:text-blue-400'
        : 'border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-neutral-400 dark:hover:text-neutral-200';
@endphp

@push('tab.titles.'.\Laravolt\Platform\Components\Tab::getActiveTab())
    <a
        {{ $attributes->merge(['class' => "whitespace-nowrap py-3 px-1 text-sm font-medium transition-colors duration-200 {$tabClasses}"]) }}
        data-hs-tab="#tab-{{ $key }}"
        aria-controls="tab-{{ $key }}"
    >
        {!! $title !!}
    </a>
@endpush

@push('tab.contents.'.\Laravolt\Platform\Components\Tab::getActiveTab())
    <div
        id="tab-{{ $key }}"
        {{ $attributes->merge(['class' => "hidden opacity-0 transition-opacity duration-200 {$isActive ? 'block opacity-100' : ''}"]) }}
        role="tabpanel"
        aria-labelledby="tab-link-{{ $key }}"
    >
        {!! $slot !!}
    </div>
@endpush
