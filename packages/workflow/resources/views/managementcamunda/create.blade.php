@extends('laravolt::layouts.app')

@section('content')
    <div class="ui segments">
        <div class="ui segment">
            <h2>Tambah Field</h2>
        </div>
        <div class="ui secondary segment">
            {!! form()->post(route('managementcamunda.store')) !!}
            {!! form()->dropdown('process_definition_key',$process_options,old('process_definition_key'))->placeholder('Select')->label('Process Definition Key')->id('process_definition_key_cmd_mng') !!}
            {!! form()->dropdown('task_name',[''=>'Select'],old('task_name'))->label('Task Name')->id('task_name_cmd_mng') !!}
            {!! form()->text('field_name',old('field_name'))->label('field_name')->id('field_name_cmd_mng') !!}
            {!! form()->dropdown('field_type', $type_options)->label('Field Type')->id('field_type_cmd_mng') !!}
            {!! form()->text('field_label')->label('Field Label')->id('field_label_cmd_mng') !!}
            {!! form()->text('segment_name')->label('Segment Group')->id('segment_group_cmd_mng') !!}
            {!! form()->text('segment_order')->label('Segment Order')->id('segment_order_cmd_mng') !!}
            {!! form()->text('field_order', old('field_order'))->label('Field Order')->id('segment_order_cmd_mng') !!}
            {!! form()->dropdown('form',$forms)->label('Form')->placeholder("Pilih Form")->hint('Hanya diisi untuk field multirow') !!}
            {!! form()->dropdown('editable',[true=>'Editable', false => 'Not Editable'], optional('editable')->editable)->label('Editable?')->id('editable') !!}
            {!! form()->textarea('validation', optional('validation')->validation)->label('Validation')->id('validation_cmd_mng')->hint("Gunakan string biasa 'required|unique:posts|max:255'")  !!}
            {!! form()->textarea('field_select_query',old('field_select_query'))->label('Select Query')->id('query') !!}
            {!! form()->dropdown('dependency', [], old('dependency'))->label('Dependency')->id('dependency')->placeholder('Select') !!}
            <label><b>Set Visibility</b></label>
            <button type="button" name="add" id="add" class="btn btn-success">Add More</button>
            <table class="table table-bordered" id="dynamicTable">
            </table>
            {!! form()->submit('submit') !!}
            {!! form()->close() !!}
        </div>
    </div>

    @push('script')
        <script type="text/javascript">
            var i = 0;
            $("#add").click(function(){
                ++i;
                $id ='field_name'+i;
                $("#dynamicTable").append('<tr class="form-dynamic"><td><select id='+$id+' name="visibility['+i+'][field_name]" placeholder="field name" class="form-control" /></td><td><input type="text" name="visibility['+i+'][field_value]" placeholder="field order" class="form-control" /></td><td><button type="button" class="btn btn-danger remove-tr">Remove</button></td></tr>');
                getField($id);
            });

            $(document).on('click', '.remove-tr', function(){
                $(this).parents('tr').remove();
            });

            $('#process_definition_key_cmd_mng').change(function(){
                var process = $(this).val();
                if(process){
                    $.ajax({
                        type:"GET",
                        url:"{{url('getTasks')}}?process="+process,
                        success:function(res){
                            if(res){
                                $("#task_name_cmd_mng").empty();
                                $("#task_name_cmd_mng").append('<option>Select</option>');
                                $.each(res,function(key,value){
                                    $("#task_name_cmd_mng").append('<option value="'+key+'">'+value+'</option>');
                                });

                            }else{
                                $("#task_name_cmd_mng").empty();
                            }
                        }
                    });
                }else{
                    $("#task_name_cmd_mng").empty();
                }
            });
            function getField($field_id) {
                if(process){
                    $.ajax({
                        type:"GET",
                        url:"{{url('getFields')}}?task="+process,
                        success:function(res){
                            if(res){
                                var x = i;
                                i = 0;
                                $.each(res,function(key,value){
                                    $("#"+$id).append('<option value="'+key+'">'+value+'</option>');
                                });

                            }else{
                                $("#"+$id).empty();
                            }
                        }
                    });
                }else{
                    $("#"+$id).empty();
                }
            }
            $('#task_name_cmd_mng').change(function(){
                var process = $(this).val();
                $('.form-dynamic').remove();
            });
        </script>
    @endpush
@endsection
