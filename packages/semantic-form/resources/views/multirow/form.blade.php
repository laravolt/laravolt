@push('head')
    <template for="{{ $name }}">
        <tr>
            @foreach($fields as $field)
                <td>{!! $field !!}</td>
            @endforeach
            @if($allowRemoval)
                <td>
                    <button class="inline-flex items-center justify-center rounded-md border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 focus:outline-hidden focus:ring-2 focus:ring-gray-200 size-7" type="button" data-role="tabular-remove-row" tabindex="-1">
                        ✕
                    </button>
                </td>
            @endif
        </tr>
    </template>
@endpush

<table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700" data-role="tabular" data-counter="{{ $limit }}">
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
                    <button class="inline-flex items-center justify-center rounded-md border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 focus:outline-hidden focus:ring-2 focus:ring-gray-200 size-7 loading disabled" type="button" data-role="tabular-remove-row" tabindex="-1">
                        ✕
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
                <button class="inline-flex w-full items-center justify-center gap-x-2 rounded-lg border border-transparent bg-blue-600 text-white px-3 py-2 text-sm hover:bg-blue-700 focus:outline-hidden focus:ring-2 focus:ring-blue-600 loading disabled" data-template="{{ $name }}" data-role="tabular-add-row" tabindex="-1">
                    + Add Row
                </button>
            </th>
        </tr>
    @endif
    </tfoot>
</table>

@once
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
                    parent.remove();
                    tabular.data('counter', counter - 1);
                });

                tabular.on('click', '[data-role="tabular-add-row"]', function (e) {
                    e.preventDefault();
                    let counter = $(e.delegateTarget).data('counter');
                    let templateName = $(e.currentTarget).data('template');
                    let template = $('template[for="' + templateName + '"]').get(0);
                    const clone = document.importNode(template.content, true);
                    $(e.delegateTarget).find('tbody').append($(clone));
                    $(e.delegateTarget).find('tbody tr:last-child').find(':input:not(:button)').each(function (idx, elm) {
                        const name = $(elm).attr('name');
                        $(elm).attr('name', name.replace('%s', counter))
                    });

                    // Re initialize UI component
                    Laravolt.init($(e.delegateTarget).find('tbody tr:last-child'));

                    $(e.delegateTarget).data('counter', counter + 1);
                });

            });
        </script>
    @endpush
@endonce
