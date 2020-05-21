<div class="ui segments shadow-none">
    @if($title)
    <div class="ui segment {{ $attributes['headerClass'] ?? '' }}">
        <h4 class="ui header p-x-1">{!! $title !!}</h4>
    </div>
    @endif
    <div class="ui segment {{ $attributes['contentClass'] ?? 'p-2' }}">
            {!! $slot !!}
    </div>
</div>
