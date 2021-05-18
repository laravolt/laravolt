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
        <div class="field">
            <label for="">&nbsp;</label>
            <div class="ui message m-t-0">Editing role are disabled by system configuration.</div>
        </div>
    @endif


    {!! form()->action(form()->submit(__('laravolt::action.save')), form()->link(__('laravolt::action.back'), route('epicentrum::users.index'))) !!}
    {!! form()->close() !!}


    <div class="ui divider section"></div>

    <div class="ui red segment p-2">
        <h4 class="ui header">@lang('laravolt::users.delete_account')</h4>

        @if($user['id'] == auth()->id())
            <div class="ui message warning">@lang('laravolt::message.cannot_delete_yourself')</div>
        @else
            {!! form()->open()->delete()->action(route('epicentrum::users.destroy', $user['id'])) !!}
            <p>Menghapus pengguna dan semua data yang berhubungan dengan pengguna ini.
                <br>
                Aksi ini tidak bisa dibatalkan.</p>
            <x-volt-button class="red" value="1">
                @lang('laravolt::action.delete') {{ $user->name }}
            </x-volt-button>
            {!! form()->close() !!}
        @endif
    </div>

@endsection
