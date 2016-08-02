<div class="ui item {{ $direction }} pointing dropdown">
    <div class="ui button icon basic">
        {!! $text !!}
    </div>
    <div class="menu">
        @foreach($menus as $menu)
        <a class="item" href="{{ $menu->url() }}">{!! $menu->title !!}</a>
            @if($menu->divider)
            <div class="divider"></div>
            @endif
        @endforeach
    </div>
</div>
