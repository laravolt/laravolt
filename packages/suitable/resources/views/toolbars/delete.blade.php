<form role="form" data-type="delete-multiple" action="{{ $url }}" method="POST" onsubmit="return confirm('Anda yakin?')">
    <input type="hidden" name="_method" value="DELETE">
    {{ csrf_field() }}
    <button type="submit" class="inline-flex items-center justify-center rounded-md border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 focus:outline-hidden focus:ring-2 focus:ring-gray-200 px-2 py-1 text-sm disabled:opacity-50 disabled:pointer-events-none" disabled>
        🗑
    </button>
</form>
