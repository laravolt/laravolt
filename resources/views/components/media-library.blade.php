<table class="ui table unstackable responsive">
    <thead>
    <tr>
        <th class="center aligned">@lang('laravolt::components/media-library.type')</th>
        <th>@lang('laravolt::components/media-library.filename')</th>
        <th>@lang('laravolt::components/media-library.filesize')</th>
        <th>&nbsp;</th>
    </tr>
    </thead>
    <tbody>
    @foreach($collection as $media)
        <tr>
            <td class="center aligned">
                <div class="ui label {{ $convertExtensionToColor($media->extension) }}">{{ $media->extension }}</div>
            </td>
            <td><a target="_blank" href="{{ $media->getUrl() }}">{{ $media->file_name }}</a></td>
            <td>{{ $media->human_readable_size }}</td>
            <td>
                @if($delete)
                    {!! form()->delete(route('media::destroy', $media->getKey())) !!}
                    {!! form()->button("<i class='icon trash'></i>")->addClass('icon secondary red')->type('submit')->onclick('return confirm("Berkas akan dihapus secara permanen. Anda yakin?")') !!}
                    {!! form()->close() !!}
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
