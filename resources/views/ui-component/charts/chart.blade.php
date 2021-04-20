<x-laravolt::panel :title="$this->title()">
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
