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
$url = route('workflow::process-definition.xml', $key)
?>

<div camunda-map-diagram-{{ $key }} style="cursor: move; height: 500px"></div>

@push('script')
<script src="https://unpkg.com/bpmn-js@6.2.1/dist/bpmn-navigated-viewer.development.js"></script>
<style>
    .highlight:not(.djs-connection) .djs-visual > :nth-child(1) {
        fill: green !important; /* color elements as green */
    }

    .highlight-overlay {
        background-color: green; /* color elements as green */
        opacity: 0.4;
        pointer-events: none; /* no pointer events, allows clicking through onto the element */
        border-radius: 10px;
    }
</style>

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
                function() {
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
