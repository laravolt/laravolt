<a {{ $attributes->merge(['href' => $url])->class(['inline-flex items-center justify-center gap-x-2 rounded-lg text-sm font-medium focus:outline-hidden transition-all disabled:opacity-50 disabled:pointer-events-none px-3.5 py-2.5 bg-blue-600 text-white hover:bg-blue-700 focus:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600', $class]) }}>
    @if($icon)
        <i class="{{ $icon }}"></i>
    @endif
    {{ $label ?? $slot }}
</a>
