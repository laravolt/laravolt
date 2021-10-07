<div themed class="x-tab">
    <div class="ui top attached secondary pointing tabular menu">
        @stack("tab.titles.$key")
    </div>

    @stack("tab.contents.$key")
</div>

@once
    @push('script')
        <script>
            $('.x-tab .menu .item').tab();
        </script>
    @endpush
@endonce

