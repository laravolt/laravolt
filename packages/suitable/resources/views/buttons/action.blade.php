@if(count($buttons) > 0)
    @if(count($buttons) > 1)
        <div class="ui icon buttons mini basic">
    @endif
            @if($view)
                <a class="ui button icon mini basic" href="{{ $view }}"><i class="eye icon"></i></a>
            @endif

            @if($edit)
                <a class="ui button icon mini basic" href="{{ $edit }}"><i class="edit icon"></i></a>
            @endif

            @if($delete)
                <form role="form" action="{{ $delete }}" method="POST" onsubmit="return confirm('Anda yakin?')">
                    <input type="hidden" name="_method" value="DELETE">
                    {{ csrf_field() }}
                    <button type="submit" class="ui button icon mini basic"><i class="delete icon"></i></button>
                </form>
            @endif
    @if(count($buttons) > 1)
        </div>
    @endif
@endif
