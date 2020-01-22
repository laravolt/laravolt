<form role="form" data-type="delete-multiple" action="{{ $url }}" method="POST" onsubmit="return confirm('Anda yakin?')">
    <input type="hidden" name="_method" value="DELETE">
    {{ csrf_field() }}
    <button type="submit" class="ui button icon disabled"><i class="icon trash"></i></button>
</form>
