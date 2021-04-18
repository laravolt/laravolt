
<x-laravolt::panel contentClass="p-0">
    <div class="ui basic segment padded">
        <div class="ui grid equal width">
            <div class="column">
                <h4 class="ui sub header">
                    {{ $this->title() }}
                </h4>
                <div class="text-6xl">1.234</div>
            </div>
            <div class="column right aligned">
                {!! form()->dropdown('test')->placeholder('30 hari')->addClass('mini compact') !!}
            </div>
        </div>
    </div>
    <div id="{{ $this->key }}"></div>
</x-laravolt::panel>

@push('script')
    @once
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endonce
    <script>
        (new ApexCharts(document.querySelector("#{{ $this->key }}"), @json($this->options()))).render();
    </script>
@endpush
