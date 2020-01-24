@extends('laravolt::layouts.app')

@section('content')
    <div class="ui segments">
        <div class="ui segment">
            <h2>Edit Field</h2>
        </div>
        <div class="ui secondary segment">
            <div class="ui form">
                {!! form()->patch(route('managementcamunda.update', $data->id)) !!}
                {!! form()->text('process_definition_key', $data->process_definition_key)->label('Process Definition Key')->id('process_definition_key_cmd_mng')->readonly() !!}
                {!! form()->text('task_name', $data->task_name)->label('Task Name')->id('task_name_cmd_mng')->readonly() !!}
                {!! form()->text('form_name', $data->form_name)->label('Form Name') !!}
                {!! form()->text('field_name', $data->field_name)->label('field_name')->id('field_name_cmd_mng')->readonly() !!}
                {!! form()->dropdown('field_type', $type_options, $data->field_type)->label('Field Type')->id('field_type_cmd_mng') !!}
                {!! form()->text('field_label', $data->field_label)->label('Field Label')->id('field_label_cmd_mng') !!}
                {!! form()->text('field_hint', $data->field_hint)->label('Field Hint')->id('field_hint_cmd_mng') !!}
                {!! form()->text('segment_group', $data->segment_group)->label('Segment Group')->id('segment_group_cmd_mng') !!}
                {!! form()->text('segment_order', $data->segment_order)->label('Segment Order')->id('field_order_cmd_mng') !!}
                {!! form()->text('field_order', $data->field_order)->label('Field Order')->id('segment_order_cmd_mng') !!}
                {!! form()->dropdown('form',$forms, optional($field_meta)->form)->label('Form')->placeholder("Pilih Form")->hint('Hanya diisi untuk field multirow') !!}
                {!! form()->dropdown('editable',[true=>'Editable', false => 'Not Editable'], optional($field_meta)->editable)->label('Editable?')->id('editable') !!}
                {!! form()->textarea('validation', optional($field_meta)->validation)->label('Validation')->id('validation_cmd_mng')->hint("Gunakan string biasa 'required|unique:posts|max:255'") !!}
                {!! form()->textarea('field_select_query', $data->field_select_query)->label('Select Query')->id('query')->hint("Pastikan ada kolom (atau alias) dengan nama id dan name.<br>Contoh: select col1 as id, col2 as name from sometable.") !!}
                {!! form()->text('dependency')->label('Dependency')->id('dependency') !!}
                <label><b>Set Visibility</b></label>
                <button type="button" name="add" id="add" class="btn btn-success">Add More</button>
                <table class="table table-bordered" id="dynamicTable">
                </table>
                {!! form()->submit('submit') !!}
                {!! form()->close() !!}
            </div>
        </div>
    </div>

    <div class="ui divider section"></div>
    <h3 class="ui header top attached block">Debugging</h3>
    <div class="ui segment bottom attached secondary">
        @dump($data->toArray())
    </div>

    @push('script')
    <script type="text/javascript">
        $(document).ready(function(){
            var i = 0;
            $("#add").click(function(){
                $id ='field_name'+i;
                $("#dynamicTable").append('<tr class="form-dynamic"><td><select id='+$id+' name="visibility['+i+'][field_name]" placeholder="field name" class="form-control" /></td><td><input type="text" name="visibility['+i+'][field_value]" placeholder="field order" class="form-control" /></td><td><button type="button" class="btn btn-danger remove-tr">Remove</button></td></tr>');
                getField($id);
                ++i;
            });
            $(document).on('click', '.remove-tr', function(){
                $(this).parents('tr').remove();
            });
            function getField($field_id){
                $.ajax({
                    type:"GET",
                    url:"{{url('getFields')}}?task="+$('#task_name_cmd_mng').val(),
                    success:function(res){
                        if(res){
                            $("#"+$field_id).empty();
                            $("#"+$field_id).append('<optio6                                                                                                                                                                                                                                             n>Select</optio6n>');
                            $.each(res,function(key,value){
                                console.log(value);
                                $("#"+$field_id).append('<option value="'+key+'">'+value+'</option>');
                            });

                        }else{
                            console.log('fail');
                            $("#"+$field_id).empty();
                        }
                    }
                });
            }
        });

    </script>
    @endpush
@endsection
