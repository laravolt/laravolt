<div id="{{ $id }}">

    @if($title || $search)
    <div class="ui menu top attached">
        @if($title)
            <div class="item borderless">
                <h4>{!! $title !!}</h4>
            </div>
        @endif
        <div class="item borderless">

        </div>
        <div class="right menu">
            @if($search)
                @include('suitable::toolbars.search', compact('search'))
            @endif
        </div>
    </div>
    @endif

    @foreach($prepends as $prepend)
        @if(view()->exists($prepend))
            @include($prepend)
        @else
            {!! $prepend !!}
        @endif
    @endforeach

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

    <div class="ui segment {{ (!$title && !$search) ? 'top':'' }} attached" style="padding: 0; overflow-y: auto">
        <table class="ui table {{ $tableClass }}" style="border: 0 none;">
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
                @if($row)
                    @include($row)
                @else
                    <tr>
                        @foreach($fields as $field)
                            <td {!! $builder->renderCellAttributes($field, $data) !!}>{!! $builder->renderCell($field, $data) !!}</td>
                        @endforeach
                    </tr>
                @endif
            @empty
                @include('suitable::empty')
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
            {!! $collection->appends(request()->input())->links($paginationView) !!}
        @endif
    </div>
    @endif
</div>

@push(config('suitable.script_placeholder'))
@include('suitable::columns.checkall.script', compact('id'))
@endpush
