<table class="ui table" data-role="tabular">
    <caption>
        <input type="hidden" name="{{ $name }}[rows]" data-role="rows-counter" value="{{ $limit }}">
    </caption>
    <thead>
    <tr>
        @foreach($labels as $label)
            <th>{!! $label !!}</th>
        @endforeach
        @if($allowRemoval)
            <th width="50px">&nbsp;</th>
        @endif
    </tr>
    </thead>
    <tbody>

    <template>
        <tr>
            @foreach($fields as $field)
                <td>{!! $field->bindAttribute('name', "") !!}</td>
            @endforeach
            @if($allowRemoval)
                <td>
                    <button class="ui button icon mini" type="button" data-role="tabular-remove-row" tabindex="-1">
                        <i class="icon remove"></i>
                    </button>
                </td>
            @endif
        </tr>
    </template>

    @foreach($rows as $fields)
        <tr>
            @foreach($fields as $field)
                <td>{!! $field->render() !!}</td>
            @endforeach
            @if($allowRemoval)
                <td>
                    <button class="ui button icon mini" type="button" data-role="tabular-remove-row" tabindex="-1">
                        <i class="icon remove"></i>
                    </button>
                </td>
            @endif
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    @if($allowAddition)
        <tr>
            <th colspan="{{ count($labels) + ($allowRemoval) }}">
                <button class="ui button fluid" data-role="tabular-add-row" tabindex="-1"><i class="icon plus"></i>
                    Tambah
                </button>
            </th>
        </tr>
    @endif
    </tfoot>
</table>

@push('script')
    <script>
      let tabular = $('[data-role="tabular"]');

      tabular.on('click', '[data-role="tabular-remove-row"]', function (e) {
        e.preventDefault();
        let counter = $(e.delegateTarget).find('[data-role="rows-counter"]');
        let parent = $(e.currentTarget).parents('tr');
        parent.fadeOut("slow", function (e) {
          parent.remove();
          counter.val(counter.val() - 1);
        });
      });

      tabular.on('click', '[data-role="tabular-add-row"]', function (e) {
        e.preventDefault();
        let counter = $(e.delegateTarget).find('[data-role="rows-counter"]');
        let template = $(e.delegateTarget).find('template').get(0);
        var clone = document.importNode(template.content, true);
        $(e.delegateTarget).find('tbody').append($(clone));
        counter.val(parseInt(counter.val()) + 1);
      });
    </script>
@endpush
