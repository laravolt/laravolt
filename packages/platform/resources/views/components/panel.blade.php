<div class="ui segments">
    <div class="ui segment {{ $attributes['headerClass'] ?? '' }}">
        <h2 class="ui header">{!! $title !!}</h2>
    </div>
    <div class="ui segment {{ $attributes['contentClass'] ?? 'p-2' }}">
            {!! $slot !!}
    </div>
</div>
