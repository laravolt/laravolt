<div id="{{ $id }}" data-role="suitable">

    @if($hasSearchableColumns)
        <form id="suitable-form-searchable" action="{{ request()->url() }}" data-role="suitable-form-searchable"></form>
    @endif

    @foreach($segments as $segment)
        <div class="ui menu {{ $loop->first ? 'top' : '' }} attached">
            {!! $segment->render() !!}
        </div>
    @endforeach

    @include('suitable::table')

    <div class="ui bottom attached menu">
        @if($showPagination && !$collection->isEmpty())
            <div class="item borderless">
                <small>{{ $builder->summary() }}</small>
            </div>
            {!! $collection->appends(request()->input())->links($paginationView) !!}
        @endif
    </div>
</div>
