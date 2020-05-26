<x-panel title="Tab">
    <div class="ui top attached secondary pointing tabular menu" themed>
        <a class="item active" data-tab="first">First</a>
        <a class="item" data-tab="second">Second</a>
        <a class="item" data-tab="third">Third</a>
    </div>
    <div class="ui bottom attached tab basic segment active" data-tab="first">
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Enim exercitationem mollitia nihil quod? Ad consectetur consequuntur distinctio excepturi illum incidunt officiis omnis quae quisquam repudiandae? Aperiam dicta eligendi explicabo perferendis.
    </div>
    <div class="ui bottom attached tab basic segment" data-tab="second">
        Second
    </div>
    <div class="ui bottom attached tab basic segment" data-tab="third">
        Third
    </div>

    <div class="ui top attached tabular menu" themed>
        <a class="item active" data-tab="a">First</a>
        <a class="item" data-tab="b">Second</a>
        <a class="item" data-tab="c">Third</a>
    </div>
    <div class="ui bottom attached tab segment active" data-tab="a">
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Enim exercitationem mollitia nihil quod? Ad consectetur consequuntur distinctio excepturi illum incidunt officiis omnis quae quisquam repudiandae? Aperiam dicta eligendi explicabo perferendis.
    </div>
    <div class="ui bottom attached tab segment" data-tab="b">
        Second
    </div>
    <div class="ui bottom attached tab segment" data-tab="c">
        Third
    </div>

</x-panel>

@push('script')
    <script>
        $('.menu .item')
            .tab()
        ;
    </script>
@endpush
