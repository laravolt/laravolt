@extends('laravolt::users.edit', ['tab' => 'password'])

@section('content-user-edit')

    <h4 class="text-lg font-semibold text-gray-800 dark:text-white">@lang('laravolt::label.reset_password_manual')</h4>
    <p class="text-sm text-gray-600 dark:text-neutral-400 mt-1 mb-4">@lang('laravolt::message.reset_password_manual_intro')</p>
    <form action="{{ route('epicentrum::password.reset', [$user['id']]) }}" method="POST">
    {{ csrf_field() }}
    <x-volt-button>@lang('laravolt::action.send_reset_password_link')</x-volt-button>
    </form>

    <div class="border-t border-gray-200 dark:border-neutral-700 my-6"></div>

    <h4 class="text-lg font-semibold text-gray-800 dark:text-white">@lang('laravolt::label.reset_password_automatic')</h4>
    <p class="text-sm text-gray-600 dark:text-neutral-400 mt-1 mb-4">@lang('laravolt::message.reset_password_automatic_intro')</p>
    {!! Form::open()->post()->action(route('epicentrum::password.generate', $user['id'])) !!}
    {{ csrf_field() }}
    <div class="mb-4">
        <label class="flex items-center gap-x-3 cursor-pointer">
            <input type="checkbox" name="must_change_password"
                   class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-neutral-800"
                   {{ request()->old('must_change_password') ? 'checked' : '' }}>
            <span class="text-sm text-gray-700 dark:text-neutral-300">@lang('laravolt::users.change_password_on_first_login')</span>
        </label>
    </div>
    <x-volt-button>@lang('laravolt::action.send_new_password')</x-volt-button>
    {!! Form::close() !!}
@endsection
