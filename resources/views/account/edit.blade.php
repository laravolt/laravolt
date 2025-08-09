@extends('laravolt::users.edit', ['tab' => 'account'])

@section('content-user-edit')

    {!! form()->bind($user)->open()->put()->action(route('epicentrum::account.update', $user['id']))->horizontal() !!}

    {!! form()->text('name')->label(__('laravolt::users.name')) !!}
    {!! form()->text('email')->label(__('laravolt::users.email')) !!}
    {!! form()->dropdown('status', $statuses)->label(__('laravolt::users.status')) !!}
    {!! form()->dropdown('timezone', $timezones)->label(__('laravolt::users.timezone')) !!}

    @if($multipleRole)
        {!! form()->checkboxGroup('roles', $roles)->label(trans('laravolt::users.roles'))->addClassIf(!$roleEditable, 'disabled') !!}
    @else
        {!! form()->radioGroup('roles', $roles)->label(trans('laravolt::users.roles'))->addClassIf(!$roleEditable, 'disabled') !!}
    @endif


    @unless($roleEditable)
        <div class="mb-3">
            <label for="">&nbsp;</label>
            <div class="rounded-md border border-yellow-200 bg-yellow-50 p-3 text-sm text-yellow-800">Editing role are disabled by system configuration.</div>
        </div>
    @endif


    {!! form()->action(form()->submit(__('laravolt::action.save')), form()->link(__('laravolt::action.back'), route('epicentrum::users.index'))) !!}
    {!! form()->close() !!}


    <div class="my-4 border-t border-gray-200"></div>

    <div class="rounded-xl border border-red-200 bg-red-50 p-3">
        <h4 class="text-sm font-semibold text-red-700">@lang('laravolt::users.delete_account')</h4>

        @if($user['id'] == auth()->id())
            <div class="mt-2 rounded-md border border-yellow-200 bg-yellow-50 p-2 text-yellow-800">@lang('laravolt::message.cannot_delete_yourself')</div>
        @else
            {!! form()->open()->delete()->action(route('epicentrum::users.destroy', $user['id'])) !!}
            <p>Menghapus pengguna dan semua data yang berhubungan dengan pengguna ini.
                <br>
                Aksi ini tidak bisa dibatalkan.</p>
            <x-volt-button class="bg-red-600 hover:bg-red-700" value="1">
                @lang('laravolt::action.delete') {{ $user->name }}
            </x-volt-button>
            {!! form()->close() !!}
        @endif
    </div>

@endsection
