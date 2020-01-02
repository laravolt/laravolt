<tr>
    <td class="numbering">{{ $loop->iteration }}</td>
    <td>{{ $data->getKey() }}</td>
    <td>
        <a href="{{ $data->getIndexUrl() }}">{{ $data->label }}</a>
        <div>{{ $data->key }}</div>
    </td>
    <td>{{ $data->process_definition_key }}</td>
    <td class="text-right">
        <a href="{{ route('workflow::module.edit', $data->getKey()) }}" class="ui primary button small"><i class="icon cogs"></i> Settings</a>
        <a href="{{ $data->getCreateUrl() }}" class="ui button small basic primary"><i class="icon plus circle"></i> New Process</a>
    </td>
</tr>
