<x-volt-panel contentClass="p-0" :title="$this->title()">
    <div id="{{ $this->key }}"></div>
</x-volt-panel>

@push('script')
    @once
        <script src="/laravolt/assets/vendor/apexcharts/dist/apexcharts.min.js"></script>
    @endonce
    <script>
        (new ApexCharts(document.querySelector("#{{ $this->key }}"), @json($this->options()))).render();
    </script>
@endpush
