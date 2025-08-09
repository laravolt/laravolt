@isset($url)
    <a href="{{ $url }}" class="block bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
@else
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
@endisset

    @if($attributes['cover'])
        <div class="relative">
            <img src="{{ $attributes['cover'] }}" alt="" class="w-full h-auto">
        </div>
    @endif

    @if($title or $content or $attributes['meta.before'] or $attributes['meta.after'])
    <div class="px-4 py-4">

        @if($attributes['meta.before'])
        <div class="text-sm text-gray-500">{!! $attributes['meta.before'] !!}</div>
        @endif

        @if($title)
        <h3 class="mt-1 text-base font-semibold text-gray-800">{{ $title }}</h3>
        @endif

        @if($attributes['meta.after'])
        <div class="mt-1 text-sm text-gray-500">{!! $attributes['meta.after'] !!}</div>
        @endif

        @if($content)
        <p class="mt-2 text-gray-700">
            {{ $content }}
        </p>
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

