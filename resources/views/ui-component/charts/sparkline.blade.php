<x-volt-panel contentClass="p-0" :title="$this->title()">
    <div id="{{ $this->key }}"></div>
</x-volt-panel>

@push('script')
    @once
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endonce
    <script>
        (new ApexCharts(document.querySelector("#{{ $this->key }}"), @json($this->options()))).render();
    </script>
@endpush
