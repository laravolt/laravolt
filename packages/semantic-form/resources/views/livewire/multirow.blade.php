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
    @foreach($this->rows as $fields)
        <tr>
            @foreach($fields as $key => $field)
                <td>{!! $field->render() !!}</td>
            @endforeach
            @if($allowRemoval)
                <td>
                    <button wire:click="removeRow({{ $key }})" class="ui button icon mini" type="button" tabindex="-1">
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
                <button wire:click.prevent="addRow" class="ui button fluid" tabindex="-1">
                    <i class="icon plus"></i>
                    Tambah
                </button>
            </th>
        </tr>
    @endif
    </tfoot>
</table>
