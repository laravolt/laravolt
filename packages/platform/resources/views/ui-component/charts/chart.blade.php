<x-laravolt::panel>
    <div id="{{ $this->key }}"></div>
</x-laravolt::panel>
@push('script')
    <script>
        var options = @json($this->options());

        var chart = new ApexCharts(document.querySelector("#{{ $this->key }}"), options);
        chart.render();

    </script>
@endpush
