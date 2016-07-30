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
            <div class="ui right aligned item">
                <form method="GET" action="">
                    <div class="ui transparent icon input">
                        <input class="prompt" name="search" value="{{ request('search') }}" type="text" placeholder="@lang('suitable::suitable.search_placeholder')">
                        <i class="search link icon"></i>
                    </div>
                </form>
            </div>
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

    <table class="ui table attached">
        <thead>
        <tr>
            @foreach($headers as $header)
                @if($header->isSortable())
                    {!! $header->getHtml() !!}
                @else
                    <th>{!! $header->getHtml() !!}</th>
                @endif
            @endforeach
        </tr>
        </thead>
        <tbody class="collection">
        @forelse($collection as $data)
            <tr>
                @foreach($fields as $field)
                    <td>{!! $builder->renderCell($field, $data) !!}</td>
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

    <div class="ui menu bottom attached">
        @if(!$collection->isEmpty())
            <div class="item borderless">
                <small>{{ $builder->summary() }}</small>
            </div>
            {!! $collection->appends(request()->input())->links('suitable::pagination') !!}
        @endif
    </div>
</div>

@push(config('suitable.script_placeholder'))
@include('suitable::checkall.script', compact('id'))
@endpush
