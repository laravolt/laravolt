<form role="form" data-type="delete-multiple" action="{{ $url }}" method="POST" onsubmit="return confirm('Anda yakin?')">
  <input type="hidden" name="_method" value="DELETE">
  {{ csrf_field() }}
  <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 focus:outline-hidden text-sm px-2.5 py-2 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-700" disabled>
    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4m-4 4v10m4-10v10"/></svg>
  </button>
</form>
