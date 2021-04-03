<div id="{{ $id }}" data-role="suitable" class="ui segments panel x-suitable">
    <div class="ui {{ config('laravolt.ui.color') }} progress sliding top attached" wire:loading.class="indeterminate">
        <div class="bar"></div>
    </div>

    <div class="ui borderless stackable menu attached">
        <div class="menu">
            <div class="item">
                @include('laravolt::ui-component.shared.searchbox', ['name' => $search])
            </div>
        </div>
        <div class="menu right">
            <div class="item">
                {!! $collection->appends(request()->input())->onEachSide(1)->links('laravolt::pagination.simple') !!}
            </div>
        </div>
    </div>

    @include('laravolt::ui-component.table-view.table')

    @if($showFooter)
        <footer class="ui bottom attached menu">
            <div class="item">
                <small>{{ $builder->summary() }}</small>
            </div>

            @if($showPerPage)
            <div class="ui item p-0">
                <div class="ui dropdown item">
                    <span class="text">{{ request('per_page', $collection->perPage()) }}</span>
                    <i class="dropdown icon" aria-hidden="true"></i>
                    <div class="menu">
                        @foreach($perPageOptions as $n)
                            <div class="item" wire:click.prevent="changePerPage({{ $n }})">
                                {{ $n }}
                            </div>
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
