<table class="ui table">
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
                <td>{!! $field->bindAttribute('name', $loop->parent->index) !!}</td>
            @endforeach
            @if($allowRemoval)
                <td>
                    <button class="ui button icon mini">
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
            <th colspan="{{ count($fields) + ($allowRemoval) }}">
                <button class="ui button fluid"><i class="icon plus"></i> Tambah</button>
            </th>
        </tr>
    @endif
    </tfoot>
</table>
