@extends('laravolt::layouts.app')

@section('content')
    <div class="ui segments">
        <div class="ui segment">
            <h2>Tambah Field</h2>
        </div>
        <div class="ui secondary segment">
            <div class="ui form">
                {!! form()->open() !!}
                {!! form()->text('process_definition_key', $data->process_definition_key)->label('Process Definition Key')->id('process_definition_key_cmd_mng')->readonly() !!}
                {!! form()->text('task_name', $data->task_name)->label('Task Name')->id('task_name_cmd_mng')->readonly() !!}
                {!! form()->text('field_name', $data->field_name)->label('field_name')->id('field_name_cmd_mng')->readonly() !!}
                {!! form()->text('field_type', $type_options, $data->field_type)->label('Field Type')->id('field_type_cmd_mng')->readonly() !!}
                {!! form()->text('field_label', $data->field_label)->label('Field Label')->id('field_label_cmd_mng')->readonly() !!}
                {!! form()->text('segment_name', $data->segment_group)->label('Segment Group')->id('segment_group_cmd_mng')->readonly() !!}
                {!! form()->text('segment_order', $data->segment_order)->label('Segment Order')->id('segment_order_cmd_mng')->readonly() !!}
                {!! form()->text('editable',[true=>'Editable', false => 'Not Editable'], optional($field_meta)->editable)->label('Editable?')->id('editable')->readonly() !!}
                {!! form()->text('field_order', $data->field_order)->label('Field Order')->id('segment_order_cmd_mng')->readonly() !!}
                {!! form()->text('editable',[true=>'Editable', false => 'Not Editable'], optional($field_meta)->editable)->label('Editable?')->id('editable')->readonly() !!}
                {!! form()->textarea('validation', optional($field_meta)->validation)->label('Validation')->id('validation_cmd_mng')->readonly() !!}
                {!! form()->textarea('field_select_query', $data->field_select_query)->label('Select Query')->id('query')->readonly() !!}
                {!! form()->text('dependency')->label('Dependency')->id('dependency')->placeholder('Select')->readonly() !!}
                {!! form()->text('validation', optional($field_meta)->validation)->label('Validation')->id('validation_cmd_mng')->readonly() !!}

                {!! form()->submit('submit') !!}
                {!! form()->close() !!}
            </div>
        </div>
    </div>
@endsection

