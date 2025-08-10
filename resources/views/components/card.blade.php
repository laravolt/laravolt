@isset($url)
    <a href="{{ $url }}" class="block rounded-xl border border-gray-200 shadow-sm hover:shadow md:transition overflow-hidden dark:border-neutral-700">
@else
    <div class="rounded-xl border border-gray-200 shadow-sm dark:border-neutral-700 overflow-hidden">
@endisset

    @if($attributes['cover'])
        <img src="{{ $attributes['cover'] }}" alt="" class="w-full h-40 object-cover">
    @endif

    @if($title or $content or $attributes['meta.before'] or $attributes['meta.after'])
    <div class="p-4">

        @if($attributes['meta.before'])
        <div class="text-xs text-gray-500 dark:text-neutral-400">{!! $attributes['meta.before'] !!}</div>
        @endif

        @if($title)
        <div class="text-lg font-semibold text-gray-800 dark:text-neutral-200">{{ $title }}</div>
        @endif

        @if($attributes['meta.after'])
        <div class="mt-1 text-xs text-gray-500 dark:text-neutral-400">{!! $attributes['meta.after'] !!}</div>
        @endif

        @if($content)
        <div class="mt-3 text-sm text-gray-600 dark:text-neutral-300">
            {{ $content }}
        </div>
        @endif

    </div>
    @endif

    {{ $body ?? null }}

    {{ $slot }}

@isset($url)
    </a>
@else
    </div>
@endisset

