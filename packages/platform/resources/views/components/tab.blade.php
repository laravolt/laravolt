<div themed class="x-tab">
    <div class="ui top attached secondary pointing tabular menu">
        @stack("tab.titles.$key")
    </div>

    @stack("tab.contents.$key")
</div>

@pushonce('script::laravolt.tab')
    <script>
        $('.menu .item').tab();
    </script>
@endpushonce

