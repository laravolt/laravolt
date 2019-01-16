<div id="{{ $id }}">

    @foreach($prepends as $prepend)
        @if(view()->exists($prepend))
            @include($prepend)
        @else
            {!! $prepend !!}
        @endif
    @endforeach

    @if(!empty($toolbars))
        <div class="ui menu attached">
            @foreach($toolbars as $toolbar)
                <div class="item borderless">
                    {!! $toolbar !!}
                </div>
            @endforeach
            <div class="menu right">
            </div>
        </div>
    @endif

    @include('suitable::table')

    @if($showPagination)
        <div class="ui menu bottom attached">
            @if(!$collection->isEmpty())
                <div class="item borderless">
                    <small>{{ $builder->summary() }}</small>
                </div>
                {!! $collection->appends(request()->input())->links($paginationView) !!}
            @endif
        </div>
    @endif
</div>
