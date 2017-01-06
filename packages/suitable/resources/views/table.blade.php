<div id="{{ $id }}">

    <div class="ui menu top attached">
        @if($title)
            <div class="item borderless">
                <h4>{!! $title !!}</h4>
            </div>
        @endif
        <div class="item borderless">

        </div>
        <div class="right menu">
            @if($showSearch)
                @include('suitable::toolbars.search')
            @endif
        </div>
    </div>

    @if(!empty($toolbars))
        <div class="ui menu attached">
            @foreach($toolbars as $toolbar)
                <div class="item borderless">
                    {!! $toolbar !!}
                </div>
            @endforeach
            <div class="menu right">
            </div>
        </div>
    @endif

    <div class="ui segment attached basic" style="padding: 0; overflow-y: auto">
        <table class="ui table attached {{ $tableClass }}" style="border-top: 0 none; border-bottom: 0 none">
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
                <tr>
                    @foreach($fields as $field)
                        <td {!! $builder->renderCellAttributes($field, $data) !!}>{!! $builder->renderCell($field, $data) !!}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($fields) }}" class="warning center aligned">
                        <div class="ui segment very padded basic">@lang('suitable::suitable.empty_message')</div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($showPagination)
    <div class="ui menu bottom attached">
        @if(!$collection->isEmpty())
            <div class="item borderless">
                <small>{{ $builder->summary() }}</small>
            </div>
            {!! $collection->appends(request()->input())->links('suitable::pagination') !!}
        @endif
    </div>
    @endif
</div>

@push(config('suitable.script_placeholder'))
@include('suitable::columns.checkall.script', compact('id'))
@endpush
