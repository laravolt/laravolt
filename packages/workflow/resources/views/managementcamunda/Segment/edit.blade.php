@extends('laravolt::layouts.app')

@section('content')
    <div class="ui segments">
        <div class="ui segment">
            <h2>Tambah Segment</h2>
        </div>
        <div class="ui secondary segment">
            {!! form()->post(route('segment.store')) !!}
            {!! form()->dropdown('process_definition_key',$process_options,old('process_definition_key'))->placeholder('Select')->label('Process Definition Key')->id('process_definition_key_cmd_mng') !!}
            {!! form()->dropdown('task_name',[''=>'Select'],old('task_name'))->label('Task Name')->id('task_name_cmd_mng') !!}
            {!! form()->text('segment_name')->label('Segment Group')->id('segment_group_cmd_mng') !!}
            {!! form()->number('segment_order')->label('Segment Order')->id('segment_order_cmd_mng') !!}
            <table class="table table-bordered" id="dynamicTable">
                <tr>
                    <th style="width: 45%">Field Name</th>
                    <th style="width: 45%">Field Order</th>
                </tr>
                <tr>
                    <td><select id='field_name_cmd_mng' name="field_name[0][field_name]" placeholder="Enter your Name" class="form-control" /></td>
                    <td><input type="number" name="field_name[0][field_order]" placeholder="Enter your Price" class="form-control" /></td>
                    <td><button type="button" name="add" id="add" class="btn btn-success">Add More</button></td>
                </tr>
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
                $("#dynamicTable").append('<tr class="form-dynamic"><td><select id='+$id+' name="field_name['+i+'][field_name]" placeholder="field name" class="form-control" /></td><td><input type="number" name="field_name['+i+'][field_order]" placeholder="field order" class="form-control" /></td><td><button type="button" class="btn btn-danger remove-tr">Remove</button></td></tr>');
                $.each($('#field_name_cmd_mng').prop("options"), function(i, opt) {
                    $("#"+$id).append('<option value="'+opt.value+'">'+opt.value+'</option>');
                });
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
                                $(".field_name_cmd_mng").empty();
                            }
                        }
                    });
                }else{
                    $("#task_name_cmd_mng").empty();
                    $("#field_name_cmd_mng").empty();
                }
            });

            $('#task_name_cmd_mng').change(function(){
                var process = $(this).val();
                if(process){
                    $.ajax({
                        type:"GET",
                        url:"{{url('getFields')}}?task="+process,
                        success:function(res){
                            if(res){
                                $("#field_name_cmd_mng").empty();
                                var x = i;
                                $('.form-dynamic').remove();
                                i = 0;
                                $("#field_name_cmd_mng").append('<option>Select</option>');
                                $.each(res,function(key,value){
                                    $("#field_name_cmd_mng").append('<option value="'+key+'">'+value+'</option>');
                                });

                            }else{
                                $("#field_name_cmd_mng").empty();
                            }
                        }
                    });
                }else{
                    $("#field_name_cmd_mng").empty();
                }
                $("#field_name_cmd_mng").val('');
            });

        </script>
    @endpush
@endsection
