<a href="{{ $action['url'] }}" class="ui button {{ $action['class'] ?? '' }}">
    @if((bool)($action['icon'] ?? false))
        <i class="icon {{ $action['icon'] }}"></i>
    @endisset
    {!! $action['label'] ?? ''!!}
</a>
