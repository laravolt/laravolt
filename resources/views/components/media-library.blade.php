<div class="overflow-x-auto">
<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
    <tr>
        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">@lang('laravolt::components/media-library.type')</th>
        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">@lang('laravolt::components/media-library.filename')</th>
        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">@lang('laravolt::components/media-library.filesize')</th>
        <th class="px-3 py-2"></th>
    </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
    @foreach($collection as $media)
        <tr>
            <td class="px-3 py-2 text-center">
                <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">{{ $media->extension }}</span>
            </td>
            <td class="px-3 py-2"><a target="_blank" href="{{ $media->getUrl() }}" class="text-teal-700 hover:text-teal-800 hover:underline">{{ $media->file_name }}</a></td>
            <td class="px-3 py-2">{{ $media->human_readable_size }}</td>
            <td class="px-3 py-2 text-right">
                @if($delete)
                    {!! form()->delete(route('media::destroy', $media->getKey())) !!}
                    {!! form()->button("<svg class='h-4 w-4' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M10 4h4m-4 0a1 1 0 011-1h2a1 1 0 011 1m-4 0H9m6 0h-1' /></svg>")
                        ->addClass('inline-flex items-center rounded-md border border-red-300 bg-white px-2 py-1 text-xs text-red-600 hover:bg-red-50')
                        ->type('submit')
                        ->onclick('return confirm("Berkas akan dihapus secara permanen. Anda yakin?")') !!}
                    {!! form()->close() !!}
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
