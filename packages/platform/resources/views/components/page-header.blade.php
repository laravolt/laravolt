<div class="ui menu secondary page-header">
    <div class="item">
        <h2 class="ui header">@yield('page.title', $title ?? "")</h2>
    </div>
    <div class="menu right">
        <div class="item">
            @foreach($actions ?? [] as $action)
                @if(is_array($action))
                    @includeWhen($action['visible'] ?? true, 'laravolt::components.button', ['action' => $action])
                @else
                    {!! $action !!}
                @endif
            @endforeach

            @stack('page.actions')
        </div>
    </div>
</div>
