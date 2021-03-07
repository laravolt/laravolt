@if($url)
    <a href="{{ $url }}" class="ui card segments panel x-laravolt::card" style="overflow: hidden">
@else
    <div class="ui card segments panel x-laravolt::card">
@endif

    @if($attributes['cover'])
        <div class="image">
            <img src="{{ $attributes['cover'] }}" alt="">
        </div>
    @endif

    @if($title or $content or $attributes['meta.before'] or $attributes['meta.after'])
    <div class="content x-laravolt::card__header">

        @if($attributes['meta.before'])
        <div class="meta x-laravolt::card__meta--before">{!! $attributes['meta.before'] !!}</div>
        @endif

        @if($title)
        <div class="header">{{ $title }}</div>
        @endif

        @if($attributes['meta.after'])
        <div class="meta x-laravolt::card__meta--after">{!! $attributes['meta.after'] !!}</div>
        @endif

        @if($content)
        <div class="description">
            {{ $content }}
        </div>
        @endif

    </div>
    @endif

    {{ $body ?? null }}

    {{ $slot }}

@if($url)
    </a>
@else
    </div>
@endif

