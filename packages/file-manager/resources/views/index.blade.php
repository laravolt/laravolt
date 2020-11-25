@if($files->isEmpty())
    <div class="ui message warning">
        @lang('Folder is empty')
    </div>
@else
    <table class="ui table padded">
        @if(!$isRoot)
            <tr>
                <td colspan="4">
                    <a href="{{ $parentUrl }}"><i class="icon up long arrow"></i> Ke Atas...</a>
                </td>
            </tr>
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
                <td class="center aligned" width="100px">
                    {!! form()->delete(route('file-manager::file.destroy', $file['key'])) !!}
                    <button type="submit" class="ui basic button icon small"
                            onclick="return confirm('Anda yakin ingin menghapus file ini?')"><i
                                class="icon circle times red"></i></button>
                    {!! form()->close() !!}
                </td>
            </tr>
        @endforeach
    </table>
@endif
