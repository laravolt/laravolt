@if($actions->isNotEmpty())
    @if($actions->count() > 1)
        <div class="ui icon buttons mini basic">
    @endif
            @if($actions->has('view'))
                <a class="ui button icon mini basic" href="{{ $actions->get('view') }}"><i class="eye icon"></i></a>
            @endif

            @if($actions->has('edit'))
                <a class="ui button icon mini basic" href="{{ $actions->get('edit') }}"><i class="edit icon"></i></a>
            @endif

            @if($actions->has('delete'))
                <form role="form" action="{{ $actions->get('delete') }}" method="POST" onsubmit="return confirm('{{ $deleteConfirmation }}')">
                    <input type="hidden" name="_method" value="DELETE">
                    {{ csrf_field() }}
                    <button type="submit" class="ui button icon mini basic"><i class="delete icon"></i></button>
                </form>
            @endif
    @if($actions->count() > 1)
        </div>
    @endif
@endif
