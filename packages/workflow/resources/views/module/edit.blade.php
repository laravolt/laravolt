@extends(
    config('laravolt.workflow.view.layout'),
    [
        '__page' => [
            'title' => __('Module'),
            'actions' => [
                [
                    'label' => __('Kembali ke Daftar Modul'),
                    'class' => '',
                    'icon' => 'icon arrow left',
                    'url' => route('workflow::module.index')
                ],
            ]
        ],
    ]
)

@section('content')
    @component('laravolt::components.panel', ['title' => __('Manage Module')])
        {!! form()->bind($module)->put(route('workflow::module.update', $module->getKey())) !!}
        {!! form()->text('key')->label('Key')->disabled() !!}
        {!! form()->text('label')->label('Label')->required() !!}

        <div class="ui field">
            <label>Hak Akses</label>
            <div class="hint">
                Di bawah ini Anda bisa mengatur hak akses untuk modul.
                <br>Setiap Role yang dicentang berhak untuk melihat data di Modul yang bersangukutan.
                <br><strong>Akses Tambahan</strong> bisa diisi dengan kombinasi:
                <span class="ui label mini basic">create</span>
                <span class="ui label mini basic">edit</span>
                <span class="ui label mini basic">delete</span>
            </div>
            <table class="ui table very compact" data-role="permissions">
                <thead>
                <tr>
                    <th>Role</th>
                    <th>Akses Tambahan</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td></td>
                    <td>
                        <div class="hint">Pisahkan dengan koma, contoh: <strong>create, edit, delete</strong></div>
                    </td>
                </tr>
                @foreach($roles as $role)
                    <tr>
                        <td>
                            <div class="ui checkbox">
                                <input type="checkbox"
                                       name="roles[{{ $role->id }}][id]"
                                       value="{{ $role->id }}" {{ ($moduleRoles->has($role->id))?'checked=checked':'' }}
                                >
                                <label>{{ $role->name }}</label>
                            </div>
                        </td>
                        <td>
                            <input type="text"
                                   name="roles[{{ $role->id }}][permission]"
                                   value="{{ \Illuminate\Support\Arr::get($moduleRoles->get($role->id), 'pivot.permission') }}"
                                   disabled
                            >
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {!! form()->action([
            form()->submit(__('Simpan')),
            form()->link(__('Kembali'), route('workflow::module.index'))
        ]) !!}

        {!! form()->close() !!}
    @endcomponent
@endsection

@push('script')
    <script>
        $(function(){

            $('[data-role="permissions"] tbody tr').on('change', 'input[type="checkbox"]', function(e){
                let checked = $(this).is(':checked');
                let textbox = $(e.delegateTarget).find('input[type="text"]');
                if (checked) {
                    textbox.removeAttr('disabled');
                    $(e.delegateTarget).addClass('warning');
                } else {
                    textbox.attr('disabled', 'disabled');
                    $(e.delegateTarget).removeClass('warning');
                }
            });

            $('[data-role="permissions"] tbody tr input[type="checkbox"]').trigger('change');
        });
    </script>
@endpush
