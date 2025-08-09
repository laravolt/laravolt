<button {{ $attributes->merge(['class' => 'inline-flex items-center gap-x-2 rounded-md bg-teal-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 disabled:opacity-50 '.$class]) }}>
    @if($icon)
        <x-volt-icon :name="$icon" />
    @endif
    {{ $label ?? $slot }}
</button>
