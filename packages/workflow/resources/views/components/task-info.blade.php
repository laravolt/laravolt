<div class="ui segments panel {{ $attributes['active'] ? 'active':'' }}">
    <div class="ui secondary segment fitted panel-header">
        <div class="ui secondary menu">
            <div class="item p-r-0"><i class="icon angle down"></i></div>
            <div class="item p-l-0"><h3 title="{{ $taskIdentifier }}">{{ $title }}</h3></div>
            <div class="right menu">
                <div class="item">
                    @if($editable && auth()->user()->can('edit', $module->getModel()))
                        <a href="{{ route('workflow::task.edit', [$module->id, $task->task_id]) }}"
                           class='ui basic button small'>
                            <i class='icon edit'></i> Edit
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="ui segment fitted panel-body" id="{{ $taskIdentifier }}">
        {!! form()->make($formDefinition)->display() !!}
    </div>
</div>

@push('body')
    <script>
        var {{ $taskIdentifier }} = new Vue({
            el: '#{{ $taskIdentifier }}',
            data: @json($values)
        });
    </script>
@endpush
