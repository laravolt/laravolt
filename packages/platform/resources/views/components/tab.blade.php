<div themed class="x-laravolt::tab">
    <div class="ui top attached secondary pointing tabular menu">
        @stack("tab.titles.$key")
    </div>

    @stack("tab.contents.$key")
</div>

@once
    @push('script')
        <script>
            $('.menu .item').tab();
        </script>
    @endpush
@endonce

