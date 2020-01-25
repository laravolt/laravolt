@push('head')
    <template for="{{ $name }}">
        <tr>
            @foreach($fields as $field)
                <td>{!! $field !!}</td>
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
@endpush

<table class="ui table" data-role="tabular" data-counter="{{ $limit }}">
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
    @foreach($rows as $fields)
        <tr>
            @foreach($fields as $field)
                <td>{!! $field->render() !!}</td>
            @endforeach
            @if($allowRemoval)
                <td>
                    <button class="ui button icon mini loading disabled" type="button" data-role="tabular-remove-row" tabindex="-1">
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
                <button class="ui button fluid loading disabled" data-template="{{ $name }}" data-role="tabular-add-row" tabindex="-1"><i
                            class="icon plus"></i>
                    Tambah
                </button>
            </th>
        </tr>
    @endif
    </tfoot>
</table>

@push('script')
    <script>
      $(function () {
        let tabular = $('[data-role="tabular"]');
        tabular.find('button.loading.disabled').removeClass('loading disabled');
        tabular.on('click', '[data-role="tabular-remove-row"]', function (e) {
          e.preventDefault();
          let tabular = $(e.delegateTarget);
          let counter = tabular.data('counter');
          let parent = $(e.currentTarget).parents('tr');
          parent.fadeOut("slow", function (e) {
            parent.remove();
            tabular.data('counter', counter - 1);
          });
        });

        tabular.on('click', '[data-role="tabular-add-row"]', function (e) {
          e.preventDefault();
          let counter = $(e.delegateTarget).data('counter');
          let templateName = $(e.currentTarget).data('template');
          let template = $('template[for="'+templateName+'"]').get(0);
          var clone = document.importNode(template.content, true);
          $(e.delegateTarget).find('tbody').append($(clone));
          $(e.delegateTarget).find('tbody tr:last-child').find(':input:not(:button)').each(function (idx, elm) {
            var name = $(elm).attr('name');
            $(elm).attr('name', name.replace('%s', counter))
          });

          // Re initialize UI component
          Laravolt.init($(e.delegateTarget).find('tbody tr:last-child'));

          $(e.delegateTarget).data('counter', counter + 1);
        });

      });
    </script>
@endpush
