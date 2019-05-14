<div id="{{ $id }}" data-role="suitable">

    @foreach($segments as $segment)
        <div class="ui menu {{ $loop->first ? 'top' : '' }} attached">
            {!! $segment->render() !!}
        </div>
    @endforeach

    @if($columns->first->isSearchable() !== null)
        <form id="suitable-form-searchable" action="{{ request()->url() }}" data-role="suitable-form-searchable">
    @endif

    @include('suitable::table')

    @if($columns->first->isSearchable() !== null)
        </form>
    @endif

    <div class="ui bottom attached menu">
        @if($showPagination && !$collection->isEmpty())
            <div class="item borderless">
                <small>{{ $builder->summary() }}</small>
            </div>
            {!! $collection->appends(request()->input())->links($paginationView) !!}
        @endif
    </div>
</div>
