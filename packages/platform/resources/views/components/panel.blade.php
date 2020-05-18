<div class="ui segments">
    <div class="ui segment {{ $attributes['headerClass'] ?? '' }}">
        <h3 class="ui header">{!! $title !!}</h3>
    </div>
    <div class="ui segment {{ $attributes['contentClass'] ?? 'p-2' }}">
            {!! $slot !!}
    </div>
</div>
