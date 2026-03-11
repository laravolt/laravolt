<x-volt-app :title="__('laravolt::menu.add_user')">

    <x-slot name="actions">
        <x-volt-backlink :url="route('epicentrum::users.index')"/>
    </x-slot>

    <x-volt-panel title="Form Tambah Pengguna" icon="user-plus">
        {!! form()->open()->post()->action(route('epicentrum::users.store'))->horizontal() !!}
        {!! form()->text('name')->label(trans('laravolt::users.name'))->required() !!}
        {!! form()->text('email')->label(trans('laravolt::users.email'))->required() !!}
        {!! form()->input('password')->appendButton(trans('laravolt::action.generate_password'), 'randomize')->label(trans('laravolt::users.password'))->required() !!}

        @if($multipleRole)
            {!! form()->checkboxGroup('roles', $roles)->label(trans('laravolt::users.roles')) !!}
        @else
            {!! form()->radioGroup('roles', $roles)->label(trans('laravolt::users.roles')) !!}
        @endif

        {!! form()->select('status', $statuses)->label(__('laravolt::users.status')) !!}
        {!! form()->select('timezone', $timezones, config('app.timezone'))->label(__('laravolt::users.timezone')) !!}

        <div class="border-t border-gray-200 dark:border-neutral-700 my-6"></div>

        <div class="space-y-3">
            <label class="block text-sm font-medium text-gray-900 dark:text-white">Opsi Tambahan</label>
            <div class="space-y-2">
                {!! form()->checkbox('send_account_information', 1)->label(__('laravolt::users.send_account_information_via_email')) !!}
                {!! form()->checkbox('must_change_password', 1)->label(__('laravolt::users.change_password_on_first_login')) !!}
            </div>
        </div>

        <div class="border-t border-gray-200 dark:border-neutral-700 my-6"></div>

        {!! form()->action(form()->submit(__('laravolt::action.save')), form()->link(__('laravolt::action.cancel'), route('epicentrum::users.index'))) !!}
        {!! form()->close() !!}

    </x-volt-panel>

    @push('body')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.randomize').forEach(function (btn) {
                    btn.addEventListener('click', function (e) {
                        const input = e.currentTarget.previousElementSibling;
                        if (input) {
                            input.value = Math.random().toString(36).substr(2, 8);
                        }
                    });
                });
            });
        </script>
    @endpush

</x-volt-app>
