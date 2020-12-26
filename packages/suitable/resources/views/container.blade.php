<div id="{{ $id }}" data-role="suitable" class="ui segments panel x-suitable">

    @if($hasSearchableColumns)
        <form id="suitable-form-searchable" action="{{ request()->url() }}" data-role="suitable-form-searchable"
              style="display: none">
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
            <div class="item">
                <small>{{ $builder->summary() }}</small>
            </div>

            @if($showPerPage)
            <div class="ui dropdown item p-0">
                <div class="ui dropdown link item">
                    <span class="text">{{ request('per_page', 15) }}</span>
                    <i class="dropdown icon"></i>
                    <div class="menu">
                        @foreach([5, 10, 15, 25, 50, 100, 250] as $n)
                            <div data-value="{!! request()->fullUrlWithQuery(['per_page' => $n]) !!}" class="item">{{ $n }}</div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {!! $collection->appends(request()->input())->links($paginationView) !!}
        </div>
    @endif
</div>
