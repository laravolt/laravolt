<a {{ $attributes->merge(['href' => $url, 'themed' => true])->class(['ui button', $class]) }}>
    @if($icon)
        <i class="icon {{ $icon }}"></i>
    @endif
        {{ $label ?? $slot }}
</a>
