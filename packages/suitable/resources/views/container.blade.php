<div id="{{ $id }}">

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
