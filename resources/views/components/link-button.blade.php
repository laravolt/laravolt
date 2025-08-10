<a {{ $attributes->merge(['href' => $url])->class(['inline-flex items-center justify-center gap-x-2 rounded-lg text-sm font-medium focus:outline-hidden transition-all disabled:opacity-50 disabled:pointer-events-none px-3.5 py-2.5 btn-accent', $class]) }}>
    @if($icon)
        <i class="{{ $icon }}"></i>
    @endif
    {{ $label ?? $slot }}
</a>
