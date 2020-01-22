<tr>
    <td class="numbering">{{ $loop->iteration }}</td>
    <td>{{ $data->getKey() }}</td>
    <td>
        <strong>{{ $data->label }}</strong>
        <div>{{ $data->key }}</div>
    </td>
    <td>{{ $data->process_definition_key }}</td>
    <td class="text-center">
        <div class="ui icon top left pointing dropdown button">
            <i class="ellipsis horizontal icon"></i>
            <div class="menu">
                <a href="{{ $data->getIndexUrl() }}" class="item"><i class="icon table"></i> Lihat Data</a>
                <a href="{{ $data->getCreateUrl() }}" class="item"><i class="icon plus"></i> Buat Proses Baru</a>
                <a href="{{ $data->getBpmnUrl() }}" class="item"><i class="icon download"></i> Download BPMN</a>
                <div class="divider"></div>
                <a href="{{ route('workflow::module.edit', $data->getKey()) }}" class="item"><i class="icon cogs"></i> Settings</a>
            </div>
        </div>
    </td>
</tr>
