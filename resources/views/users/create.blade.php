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

        <div class="my-4 border-t border-gray-200"></div>

        <div class="space-y-2">
            <label for="">Opsi Tambahan</label>
            <div>
                {!! form()->checkbox('send_account_information', 1)->label(__('laravolt::users.send_account_information_via_email')) !!}
                {!! form()->checkbox('must_change_password', 1)->label(__('laravolt::users.change_password_on_first_login')) !!}
            </div>
        </div>

        <div class="my-4 border-t border-gray-200"></div>

        {!! form()->action(form()->submit(__('laravolt::action.save')), form()->link(__('laravolt::action.cancel'), route('epicentrum::users.index'))) !!}
        {!! form()->close() !!}

    </x-volt-panel>

</x-volt-app>>


