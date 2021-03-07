<div id="{{ $id }}" data-role="suitable" class="ui segments panel x-suitable">

    @foreach($segments as $segment)
        @unless($segment->isEmpty())
            <div class="ui borderless stackable menu {{ $loop->first ? 'top' : '' }} attached">
                {!! $segment->render() !!}
                <div class="menu right">
                    <div class="item">
                        {!! $collection->appends(request()->input())->onEachSide(1)->links('laravolt::pagination.simple') !!}
                    </div>
                </div>
            </div>
        @endunless
    @endforeach

    @include('suitable::table')

    @if($showFooter)
        <footer class="ui bottom attached menu">
            <div class="item">
                <small>{{ $builder->summary() }}</small>
            </div>

            @if($showPerPage)
            <div class="ui dropdown item p-0">
                <div class="ui dropdown link item">
                    <span class="text">{{ request('per_page', $collection->perPage()) }}</span>
                    <i class="dropdown icon"></i>
                    <div class="menu">
                        @foreach($perPageOptions as $n)
                            <div data-value="{!! request()->fullUrlWithQuery(['per_page' => $n]) !!}" class="item">{{ $n }}</div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {!! $collection->appends(request()->input())->onEachSide(1)->links($paginationView) !!}
        </footer>
    @endif
</div>

@if($hasSearchableColumns)
    <form id="suitable-form-searchable" action="{{ request()->url() }}" data-role="suitable-form-searchable"
          style="display: none">
        <input type="submit">
    </form>
@endif
