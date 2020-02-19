<?php

$query = "SELECT DISTINCT process_definition_key, task_name, count
            FROM workflow_module JOIN (
                SELECT process_definition_key, task_name, count(1) count
                FROM camunda_task WHERE status = 'NEW'
                    AND task_id IS NOT NULL
                    GROUP BY process_definition_key, task_name
            ) task_counter USING (process_definition_key)
        WHERE process_definition_key = '$key'";

$counter = collect(\DB::select($query));

$nodes = optional(\Laravolt\Workflow\Models\Bpmn::where('process_definition_key', $key)->first())->nodes;
$callActivity = collect($nodes)->filter(function ($node) {
    return \Illuminate\Support\Arr::get($node, 'type') === 'callActivity';
});

if ($callActivity->isNotEmpty()) {
    $callActivityKeys = $callActivity->transform(function ($item) {
        return "\"".$item['id']."\"";
    })->implode(',');
    $subProcessQuery = "SELECT DISTINCT process_definition_key, process_definition_key as task_name, count
            FROM workflow_module JOIN (
                SELECT process_definition_key, task_name, count(1) count
                FROM camunda_task WHERE status = 'NEW'
                    AND task_id IS NOT NULL
                    GROUP BY process_definition_key, task_name
            ) task_counter USING (process_definition_key)
        WHERE process_definition_key in (".$callActivityKeys.")";
    $counter = $counter->concat(collect(\DB::select($subProcessQuery)));
}

$url = route('workflow::process-definition.xml', $key)
?>

<div camunda-map-diagram-{{ $key }} style="cursor: move; height: 500px"></div>

@push('script')
    <script src="https://unpkg.com/bpmn-js@6.2.1/dist/bpmn-navigated-viewer.development.js"></script>
    <script>
        // Render diagram
        var viewer_{{ $key }} = new BpmnJS({
            container: '[camunda-map-diagram-{{ $key }}]'
        });

        // load + show diagram
        $.get("{{ $url }}", showDiagram, 'text');

        function showDiagram(diagramXML) {
            viewer_{{ $key }}.importXML(diagramXML, function () {
                var overlays = viewer_{{ $key }}.get('overlays');
                var elementRegistry = viewer_{{ $key }}.get('elementRegistry');

                viewer_{{ $key }}.get('canvas').zoom('fit-viewport');

                        @foreach($counter as $task)
                var shape = elementRegistry.get('{{ $task->task_name }}');
                $overlayHtml = $('<div bpmn-diagram-counter data-task-name="{{ $task->task_name }}" class="ui teal circular label big">').html({{ $task->count }});
                overlays.add(
                    '{{ $task->task_name }}',
                    {
                        position: {
                            right: 20,
                            bottom: 20
                        },
                        html: $overlayHtml
                    }
                );

                setTimeout(
                    function () {
                        $('[bpmn-diagram-counter][data-task-name="{{ $task->task_name }}"]')
                            .transition('set looping')
                            .transition('pulse', '2000ms')
                        ;
                    },
                    (Math.random() * 1000)
                );

                @endforeach

            });
        }
    </script>
@endpush
