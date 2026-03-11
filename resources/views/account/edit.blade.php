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
        <div class="rounded-lg border border-blue-200 bg-blue-50 p-3 dark:bg-blue-900/50 dark:border-blue-800">
            <p class="text-sm text-blue-700 dark:text-blue-300">Editing role are disabled by system configuration.</p>
        </div>
    @endif


    {!! form()->action(form()->submit(__('laravolt::action.save')), form()->link(__('laravolt::action.back'), route('epicentrum::users.index'))) !!}
    {!! form()->close() !!}


    <div class="border-t border-gray-200 dark:border-neutral-700 my-6"></div>

    <div class="rounded-xl border border-red-200 bg-red-50 p-5 dark:bg-red-900/30 dark:border-red-800">
        <h4 class="text-base font-semibold text-red-800 dark:text-red-300 mb-3">@lang('laravolt::users.delete_account')</h4>

        @if($user['id'] == auth()->id())
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 dark:bg-amber-900/50 dark:border-amber-800">
                <p class="text-sm text-amber-700 dark:text-amber-300">@lang('laravolt::message.cannot_delete_yourself')</p>
            </div>
        @else
            {!! form()->open()->delete()->action(route('epicentrum::users.destroy', $user['id'])) !!}
            <p class="text-sm text-gray-700 dark:text-neutral-300 mb-4">Menghapus pengguna dan semua data yang berhubungan dengan pengguna ini.
                <br>
                Aksi ini tidak bisa dibatalkan.</p>
            <x-volt-button variant="danger" value="1">
                @lang('laravolt::action.delete') {{ $user->name }}
            </x-volt-button>
            {!! form()->close() !!}
        @endif
    </div>

@endsection
