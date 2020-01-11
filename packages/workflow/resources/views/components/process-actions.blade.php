@can('view', $module->getModel())
    <a href="{{ route('workflow::process.show', [$module->id, $data->process_instance_id]) }}" class="ui icon button mini primary">
        <i class="icon eye"></i>
    </a>
@endcan
@can('edit', $module->getModel())
    <a href="{{ route('workflow::process.show', [$module->id, $data->process_instance_id]) }}" class="ui icon button mini basic primary">
        <i class="icon edit"></i>
    </a>
@endcan
