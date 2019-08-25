<div id="{{ $id }}" data-role="suitable">

    @if($hasSearchableColumns)
        <form id="suitable-form-searchable" action="{{ request()->url() }}" data-role="suitable-form-searchable" style="display: none">
            <input type="submit">
        </form>
    @endif

    @foreach($segments as $segment)
        @unless($segment->isEmpty())
        <div class="ui borderless menu {{ $loop->first ? 'top' : '' }} attached">
            {!! $segment->render() !!}
        </div>
        @endunless
    @endforeach

    @include('suitable::table')

    @if($showFooter)
        <div class="ui bottom attached menu">
            <div class="item borderless">
                <small>{{ $builder->summary() }}</small>
            </div>
            {!! $collection->appends(request()->input())->links($paginationView) !!}
        </div>
    @endif
</div>
