<div id="{{ $id }}">

    @if($title || $search)
    <div class="ui menu top attached">
        @if($title)
            <div class="item borderless">
                <h4>{!! $title !!}</h4>
            </div>
        @endif
        <div class="item borderless">

        </div>
        <div class="right menu">
            @if($search)
                @include('suitable::toolbars.search', compact('search'))
            @endif
        </div>
    </div>
    @endif

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

    <div class="ui segment {{ (!$title && !$search) ? 'top':'' }} {{ (!$showPagination) ? 'bottom':'' }} attached" style="padding: 0; overflow-y: auto">
        @include('suitable::table')
    </div>

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

@push(config('suitable.script_placeholder'))
@include('suitable::columns.checkall.script', compact('id'))
@endpush
