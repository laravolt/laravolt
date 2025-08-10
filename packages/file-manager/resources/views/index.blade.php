@if($files->isEmpty())
    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
        @lang('Folder is empty')
    </div>
@else
    <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
        @if(!$isRoot)
            <thead>
            <tr>
                <th colspan="4">
                    <a class="inline-flex items-center gap-x-1 text-sm text-blue-600 hover:underline" href="{{ $parentUrl }}">↑ Ke Atas...</a>
                </th>
            </tr>
            </thead>
        @endif

        @foreach($files as $file)
            <tr>
                <td>
                    <a href="{{ $file['permalink'] }}">
                        <i class="icon {{ $file['class'] }}"></i>{{ $file['name'] }}
                    </a>
                </td>
                <td>{{ $file['size_for_human'] }}</td>
                <td>{{ $file['modified_formatted'] }}</td>
                <td class="text-center" width="120">
                    {!! form()->delete(route('file-manager::file.destroy', $file['key'])) !!}
                    <button type="submit" class="inline-flex items-center justify-center rounded-md border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 focus:outline-hidden focus:ring-2 focus:ring-gray-200 px-2 py-1 text-xs" onclick="return confirm('Anda yakin ingin menghapus file ini?')">Hapus</button>
                    {!! form()->close() !!}
                </td>
            </tr>
        @endforeach
    </table>
@endif
