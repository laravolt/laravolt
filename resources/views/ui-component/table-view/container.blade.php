<div data-role="suitable" class="ui segments panel x-suitable">
    <div class="ui {{ config('laravolt.ui.color') }} progress swinging top attached" wire:loading.class="indeterminate">
        <div class="bar"></div>
    </div>

    @if($this->filters() || $this->showSearchbox)
        <div class="ui borderless unstackable menu attached" data-role="suitable-header">
            <div class="menu">
                @if($this->showSearchbox)
                    <div class="item">
                        @include('laravolt::ui-component.shared.searchbox')
                    </div>
                @endif
            </div>
            <div class="menu right">
                <div class="item">
                    @include('laravolt::ui-component.table-view.filter')
                </div>
            </div>
        </div>
    @endif

    @include('laravolt::ui-component.table-view.table')

    <footer class="ui bottom attached menu">
        @if($data instanceof \Illuminate\Contracts\Pagination\Paginator)
            <div class="item">
                <small>{{ $this->summary() }}</small>
            </div>

            @if($showPerPage)
                <div class="ui item p-0">
                    <div class="ui dropdown item">
                        <span class="text">{{ request('per_page', $data->perPage()) }}</span>
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

            {!! $data->appends(request()->input())->onEachSide(1)->links($paginationView) !!}
        @endif
    </footer>

</div>
