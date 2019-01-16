<div id="{{ $id }}">

    @include('suitable::table')

    <div class="ui menu bottom attached">
        @if($showPagination)
            @if(!$collection->isEmpty())
                <div class="item borderless">
                    <small>{{ $builder->summary() }}</small>
                </div>
                {!! $collection->appends(request()->input())->links('suitable::pagination.full') !!}
            @endif
        @endif
    </div>
</div>
