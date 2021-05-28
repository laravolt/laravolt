<x-volt-public>

    <x-volt-workflow-diagram-button :instance="$instance"></x-volt-workflow-diagram-button>

    <x-volt-panel title="Tracking" icon="compass">
        <div class="ui relaxed divided list">
            @foreach($completedTasks as $completedTask)
                <div class="item">
                    <div class="image" style="color: green">
                        <x-volt-icon name="check-circle"/>
                    </div>
                    <div class="content">
                        <div class="header">{{ $completedTask->name }}</div>
                        <div class="description">{{ $completedTask->endTime->isoFormat('dddd, DD MMMM YYYY HH:MM') }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-volt-panel>

</x-volt-public>
