<table class="ui top attached table">
    <thead>
    <tr>
        @foreach($headers as $header)
            @if($header->isSortable())
                {!! $header->getHtml() !!}
            @else
                <th {!! $header->renderAttributes() !!}>{!! $header->getHtml() !!}</th>
            @endif
        @endforeach
    </tr>
    </thead>
    <tbody class="collection">
    @forelse($collection as $data)
        @php($outerLoop = $loop)
        @if($row)
            @include($row)
        @else
            <tr>
                @foreach($fields as $field)
                    <td {!! $builder->renderCellAttributes($field, $data) !!}>{!! $builder->renderCell($field, $data, $collection, $outerLoop) !!}</td>
                @endforeach
            </tr>
        @endif
    @empty
        @include('suitable::empty')
    @endforelse
    </tbody>
</table>
