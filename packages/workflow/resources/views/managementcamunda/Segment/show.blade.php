@extends('laravolt::layouts.app')

@section('content')
    <div class="ui segments">
        <div class="ui secondary segment">
            {!! form() !!}
            {!! form()->text('process_definition_key',old('process_definition_key'))->placeholder('Select')->label('Process Definition Key')->id('process_definition_key_cmd_mng') !!}
            {!! form()->text('task_name',[''=>'Select'],old('task_name'))->label('Task Name')->id('task_name_cmd_mng') !!}
            {!! form()->text('segment_name')->label('Segment Group')->id('segment_group_cmd_mng') !!}
            {!! form()->number('segment_order')->label('Segment Order')->id('segment_order_cmd_mng') !!}
            <table class="table table-bordered" id="dynamicTable">
                <tr>
                    <th style="width: 45%">Field Name</th>
                    <th style="width: 45%">Field Order</th>
                </tr>
                {{--<tr>
                    <td><select id='field_name_cmd_mng' name="field_name[0][field_name]" placeholder="Enter your Name" class="form-control" /></td>
                    <td><input type="number" name="field_name[0][field_order]" placeholder="Enter your Price" class="form-control" /></td>
                    <td><button type="button" name="add" id="add" class="btn btn-success">Add More</button></td>
                </tr>--}}
            </table>
            {!! form()->submit('submit') !!}
            {!! form()->close() !!}
        </div>
    </div>

@endsection
