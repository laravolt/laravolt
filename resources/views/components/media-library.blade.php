<table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
    <thead>
    <tr>
        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">@lang('laravolt::components/media-library.type')</th>
        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">@lang('laravolt::components/media-library.filename')</th>
        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-neutral-400">@lang('laravolt::components/media-library.filesize')</th>
        <th class="px-3 py-2"></th>
    </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
    @foreach($collection as $media)
        <tr>
            <td class="px-3 py-2">
                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded bg-gray-100 text-gray-800 dark:bg-neutral-700 dark:text-neutral-300">{{ $media->extension }}</span>
            </td>
            <td class="px-3 py-2 text-sm text-gray-700 dark:text-neutral-300"><a target="_blank" class="text-blue-600 hover:underline dark:text-blue-500" href="{{ $media->getUrl() }}">{{ $media->file_name }}</a></td>
            <td class="px-3 py-2 text-sm text-gray-700 dark:text-neutral-300">{{ $media->human_readable_size }}</td>
            <td class="px-3 py-2 text-right">
                @if($delete)
                    {!! form()->delete(route('media::destroy', $media->getKey())) !!}
                    {!! form()->button("<svg class=\"size-4\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3m-4 0h14\"/></svg>")->addClass('px-3 py-2 inline-flex items-center gap-x-2 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 focus:outline-hidden dark:text-red-400 dark:hover:bg-red-900/20')->type('submit')->onclick('return confirm("Berkas akan dihapus secara permanen. Anda yakin?")') !!}
                    {!! form()->close() !!}
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
